<?php

namespace App\Http\Controllers;

use App\Models\Preorder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\PreorderUserConfirmation;
use App\Mail\PreorderAdminNotification;

class PreorderController extends Controller
{
    public function index()
    {
        return view('preorder.index');
    }

    public function store(Request $request)
    {
        // Force JSON pour AJAX
        $request->merge(['ajax' => true]);

        $validated = $request->validate([
            'firstName' => 'required|string|max:255|min:2',
            'lastName' => 'required|string|max:255|min:2',
            'email' => 'required|email:rfc|max:255|unique:preorders,email',
            'phone' => 'nullable|string|max:20',
            'projectNote' => 'nullable|string|max:1000',
            'total_kg' => 'required|integer|min:1|max:99999',
            'fruit_kg' => 'required|integer|min:0|max:99999',
            'puree_kg' => 'required|integer|min:0|max:99999',
            'lapin_kg' => 'required|integer|min:0|max:99999',
            'poulet_goliath_kg' => 'required|integer|min:0|max:99999',
            'deliveries' => 'required|array|min:1|max:20',
            'deliveries.*.productType' => 'required|in:fruit,puree,lapin,poulet_goliath',
            'deliveries.*.quantityKg' => 'required|integer|min:1|max:5000',
            'deliveries.*.deliveryDate' => 'required|date',
            'deliveries.*.deliveryAddress' => 'required|string|max:500|min:5',
            'optional_prepayment' => 'required|numeric|min:0|max:9999999'
        ], [
            'email.unique' => 'Cet email est déjà utilisé.',
            'deliveries.required' => 'Ajoutez au moins 1 livraison.',
            'firstName.min' => 'Prénom trop court (min 2 caractères).'
        ]);

        $threeMonthsLater = now()->addMonths(3)->startOfDay();
        $oneWeekLater = now()->addWeeks(1)->startOfDay();

        foreach ($validated['deliveries'] as $index => $delivery) {
            $date = \Carbon\Carbon::parse($delivery['deliveryDate'])->startOfDay();
            $type = $delivery['productType'];
            $productLabel = match($type) {
                'fruit' => 'Tomate fruit',
                'puree' => 'Tomate purée',
                'lapin' => 'Lapin',
                'poulet_goliath' => 'Poulet Goliath',
                default => $type
            };

            if (in_array($type, ['fruit', 'puree'])) {
                if ($date->lt($threeMonthsLater)) {
                    return response()->json([
                        'success' => false,
                        'errors' => ["deliveries.{$index}.deliveryDate" => ["Pour {$productLabel}, la date de livraison doit être d'au moins 3 mois après la date du jour (" . $threeMonthsLater->format('d/m/Y') . ")."]]
                    ], 422);
                }
            } else {
                if ($date->lt($oneWeekLater)) {
                    return response()->json([
                        'success' => false,
                        'errors' => ["deliveries.{$index}.deliveryDate" => ["Pour {$productLabel}, la date de livraison doit être d'au moins 1 semaine après la date du jour (" . $oneWeekLater->format('d/m/Y') . ")."]]
                    ], 422);
                }
            }
        }

        // ✅ Vérif POST-validation (plus simple)
        $fruitKg = $validated['fruit_kg'];
        $pureeKg = $validated['puree_kg'];
        $lapinKg = $validated['lapin_kg'];
        $pouletGoliathKg = $validated['poulet_goliath_kg'];
        $expectedTotal = $fruitKg + $pureeKg + $lapinKg + $pouletGoliathKg;

        if ($validated['total_kg'] != $expectedTotal) {
            return response()->json([
                'success' => false,
                'errors' => ['total_kg' => ["Total ({$validated['total_kg']}kg) ≠ Somme des produits ({$expectedTotal}kg)"]]
            ], 422);
        }

        try {
            $preorder = Preorder::create([
                'first_name' => $validated['firstName'],
                'last_name' => $validated['lastName'],
                'email' => strtolower($validated['email']),
                'phone' => $validated['phone'] ?? null,
                'project_note' => $validated['projectNote'],
                'total_kg' => (int) $validated['total_kg'],
                'fruit_kg' => (int) $validated['fruit_kg'],
                'puree_kg' => (int) $validated['puree_kg'],
                'lapin_kg' => (int) $validated['lapin_kg'],
                'poulet_goliath_kg' => (int) $validated['poulet_goliath_kg'],
                'deliveries' => $validated['deliveries'],
                'optional_prepayment' => (float) $validated['optional_prepayment'],
                'status' => 'pending',
                'submitted_at' => now(),
                'ip_address' => $request->ip()
            ]);

            Log::info('Précommande créée', ['id' => $preorder->id, 'email' => $validated['email']]);

            return response()->json([
                'success' => true,
                'message' => 'Précommande enregistrée avec succès !',
                'preorder_id' => $preorder->id,
                'total_prepay' => $preorder->optional_prepayment
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erreur création précommande', [
                'error' => $e->getMessage(),
                'email' => $validated['email'] ?? 'unknown'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur. Réessayez ou contactez-nous.'
            ], 500);
        }
    }

    public function paydunya(Request $request)
    {
        $request->validate([
            'preorder_id' => 'required|exists:preorders,id'
        ]);

        $preorder = Preorder::findOrFail($request->preorder_id);

        if ($preorder->optional_prepayment <= 0) {
            return response()->json(['success' => false, 'message' => 'Montant invalide.'], 400);
        }

        $masterKey = config('services.paydunya.master_key');
        if (!$masterKey) {
            Log::warning('Clés PayDunya non configurées dans .env. Simulation de l\'URL de paiement.');
            return response()->json([
                'success' => true,
                'url' => route('preorder.success', ['id' => $preorder->id, 'simulated' => 1])
            ]);
        }

        $this->setupPaydunya();

        $invoice = new \Paydunya\Checkout\CheckoutInvoice();
        $invoice->addItem("Acompte précommande Produits Agricoles (#{$preorder->id})", 1, $preorder->optional_prepayment, $preorder->optional_prepayment, "Acompte pour sécurisation prioritaire de la précommande");
        $invoice->setTotalAmount($preorder->optional_prepayment);
        $invoice->addCustomData("preorder_id", $preorder->id);
        $invoice->setReturnUrl(route('preorder.success', ['id' => $preorder->id]));
        $invoice->setCancelUrl(route('preorder.thankYou', ['id' => $preorder->id, 'cancel' => 1]));
        $invoice->setCallbackUrl(route('preorder.ipn'));

        if ($invoice->create()) {
            return response()->json([
                'success' => true,
                'url' => $invoice->getInvoiceUrl()
            ]);
        }

        Log::error('Erreur SDK PayDunya (create)', ['response_text' => $invoice->response_text]);
        return response()->json([
            'success' => false,
            'message' => 'Erreur de communication avec PayDunya : ' . $invoice->response_text
        ], 500);
    }

    public function success(Request $request)
    {
        $preorder = Preorder::findOrFail($request->id);

        $token = $request->query('token');

        if (!$token) {
            if ($request->query('simulated') == 1) {
                $preorder->status = 'paid';
                $preorder->save();
                $this->sendConfirmationEmails($preorder);
                return view('preorder.thank_you', compact('preorder'));
            }
            return redirect()->route('preorder.index')->with('error', 'Token de paiement manquant.');
        }

        $this->setupPaydunya();

        $invoice = new \Paydunya\Checkout\CheckoutInvoice();
        if ($invoice->confirm($token)) {
            if ($invoice->getStatus() === 'completed') {
                $preorder->status = 'paid';
                $preorder->receipt_url = $invoice->getReceiptUrl();
                $preorder->save();
                $this->sendConfirmationEmails($preorder);
                return view('preorder.thank_you', compact('preorder'));
            } else {
                Log::warning('Paiement PayDunya non complété', ['status' => $invoice->getStatus(), 'token' => $token]);
                return redirect()->route('preorder.thankYou', ['id' => $preorder->id, 'status' => $invoice->getStatus()]);
            }
        }

        Log::error('Erreur de confirmation PayDunya', ['response_text' => $invoice->response_text, 'token' => $token]);
        return redirect()->route('preorder.index')->with('error', 'Échec de la vérification du paiement auprès de PayDunya.');
    }

    public function thankYou(Request $request)
    {
        $preorder = Preorder::findOrFail($request->id);

        $this->sendConfirmationEmails($preorder);

        return view('preorder.thank_you', compact('preorder'));
    }

    public function ipn(Request $request)
    {
        try {
            $masterKey = config('services.paydunya.master_key');
            $receivedHash = $request->input('data.hash');
            $expectedHash = hash('sha512', $masterKey);

            if ($receivedHash === $expectedHash) {
                if ($request->input('data.status') === 'completed') {
                    $preorderId = $request->input('data.custom_data.preorder_id');
                    $preorder = Preorder::find($preorderId);

                    if ($preorder && $preorder->status !== 'paid') {
                        $preorder->status = 'paid';
                        $preorder->receipt_url = $request->input('data.receipt_url');
                        $preorder->save();
                        $this->sendConfirmationEmails($preorder);
                        Log::info('IPN PayDunya : Paiement validé avec succès', ['preorder_id' => $preorderId]);
                    }
                }
                return response()->json(['success' => true]);
            } else {
                Log::warning('IPN PayDunya : Hash invalide', ['received' => $receivedHash]);
                return response()->json(['error' => 'Hash invalide'], 403);
            }
        } catch (\Exception $e) {
            Log::error('IPN PayDunya Erreur', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    private function setupPaydunya()
    {
        $masterKey = config('services.paydunya.master_key') ?: 'MASTER_KEY_SIMULATION';
        $publicKey = config('services.paydunya.public_key') ?: 'PUBLIC_KEY_SIMULATION';
        $privateKey = config('services.paydunya.private_key') ?: 'PRIVATE_KEY_SIMULATION';
        $token = config('services.paydunya.token') ?: 'TOKEN_SIMULATION';
        $mode = config('services.paydunya.mode', 'test');

        \Paydunya\Setup::setMasterKey($masterKey);
        \Paydunya\Setup::setPublicKey($publicKey);
        \Paydunya\Setup::setPrivateKey($privateKey);
        \Paydunya\Setup::setToken($token);
        \Paydunya\Setup::setMode($mode);

        \Paydunya\Checkout\Store::setName(config('app.name', 'Ferme de l\'Amitié'));
        \Paydunya\Checkout\Store::setTagline("Engagement, Qualité et Transparence");
        \Paydunya\Checkout\Store::setCallbackUrl(route('preorder.ipn'));
    }

    private function sendConfirmationEmails(Preorder $preorder)
    {
        if (!$preorder->confirmation_sent) {
            try {
                Mail::to($preorder->email)->send(new PreorderUserConfirmation($preorder));
                Mail::to('fermedelamitie@contact.com')->send(new PreorderAdminNotification($preorder));
                $preorder->confirmation_sent = true;
                $preorder->save();
            } catch (\Exception $e) {
                Log::error('Erreur envoi email confirmation', ['error' => $e->getMessage()]);
            }
        }
    }
}
