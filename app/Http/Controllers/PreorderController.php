<?php

namespace App\Http\Controllers;

use App\Models\Preorder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            'phone' => 'nullable|regex:/^01[0-9]{8}$/|size:10',
            'projectNote' => 'nullable|string|max:1000',
            'total_kg' => 'required|integer|min:1|max:9999',
            'fruit_kg' => 'required|integer|min:0|max:9999',     // ✅ 0 OK
            'puree_kg' => 'required|integer|min:0|max:9999',     // ✅ 0 OK 
            'deliveries' => 'required|array|min:1|max:20',
            'deliveries.*.productType' => 'required|in:fruit,puree',
            'deliveries.*.quantityKg' => 'required|integer|min:1|max:5000',
            'deliveries.*.deliveryDate' => 'required|date|after:2026-06-30',
            'deliveries.*.deliveryAddress' => 'required|string|max:500|min:5',
            'optional_prepayment' => 'required|numeric|min:0|max:999999'
        ], [
            'fruit_kg.min' => 'Fruit minimum 0 kg.',
            'puree_kg.min' => 'Purée minimum 0 kg.',
            'phone.regex' => 'Téléphone invalide : commencez par 01 + 8 chiffres (ex: 0123456789).',
            'phone.size' => 'Téléphone doit faire exactement 10 chiffres.',
            'deliveries.*.deliveryDate.after' => 'Date livraison doit être après le 30 Juin 2026.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'deliveries.required' => 'Ajoutez au moins 1 livraison.',
            'firstName.min' => 'Prénom trop court (min 2 caractères).'
        ]);

        // ✅ Vérif POST-validation (plus simple)
        $fruitKg = $validated['fruit_kg'];
        $pureeKg = $validated['puree_kg'];
        $expectedTotal = $fruitKg + $pureeKg;

        if ($validated['total_kg'] != $expectedTotal) {
            return response()->json([
                'success' => false,
                'errors' => ['total_kg' => ["Total ({$validated['total_kg']}kg) ≠ Fruit ({$fruitKg}kg) + Purée ({$pureeKg}kg)"]]
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
}
