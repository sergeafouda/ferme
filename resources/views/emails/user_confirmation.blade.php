<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirmation de précommande</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f9f9f9; }
        .container { max-width: 600px; margin: 30px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        h1 { color: #842114; font-size: 24px; margin-bottom: 20px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; }
        h2 { color: #444; font-size: 18px; margin-top: 25px; margin-bottom: 15px; }
        p { margin-bottom: 15px; font-size: 15px; }
        .box { background: #fdf8f7; border-left: 4px solid #842114; padding: 15px 20px; margin: 20px 0; border-radius: 0 8px 8px 0; }
        .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table th, .table td { padding: 12px 15px; border-bottom: 1px solid #eee; text-align: left; font-size: 15px; }
        .table th { background-color: #fcfcfc; color: #666; font-weight: 600; }
        .badge { display: inline-block; padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 13px; }
        .badge.paid { background-color: #e6f4ea; color: #137333; }
        .badge.pending { background-color: #fef7e0; color: #b06000; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; font-size: 13px; color: #888; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ferme de l'Amitié - Confirmation de précommande</h1>
        
        <p>Bonjour <strong>{{ $preorder->first_name }} {{ $preorder->last_name }}</strong>,</p>
        
        <p>Nous vous remercions pour votre précommande de produits agricoles à la Ferme de l'Amitié. Votre demande a bien été enregistrée dans notre système.</p>

        <div class="box">
            <strong>Statut de la réservation :</strong> 
            @if($preorder->status === 'paid')
                <span class="badge paid">Acompte Payé (Priorité Absolue)</span>
                @if($preorder->receipt_url)
                    <p style="margin-top: 15px;"><a href="{{ $preorder->receipt_url }}" target="_blank" style="display: inline-block; background-color: #842114; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold;">📄 Télécharger ma facture PDF PayDunya</a></p>
                @endif
            @else
                <span class="badge pending">Enregistrée (Paiement en attente / Sans acompte)</span>
                <p style="margin-top: 10px; font-size: 14px; color: #666;">Note : Vous pouvez à tout moment sécuriser votre précommande et obtenir un traitement prioritaire en réglant l'acompte de {{ number_format($preorder->optional_prepayment, 0, ',', ' ') }} FCFA.</p>
            @endif
        </div>

        <h2>Récapitulatif de votre commande (#{{ $preorder->id }})</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité totale</th>
                </tr>
            </thead>
            <tbody>
                @if($preorder->fruit_kg > 0)
                <tr>
                    <td>Tomate fruit</td>
                    <td>{{ $preorder->fruit_kg }} kg</td>
                </tr>
                @endif
                @if($preorder->puree_kg > 0)
                <tr>
                    <td>Tomate en purée</td>
                    <td>{{ $preorder->puree_kg }} kg</td>
                </tr>
                @endif
                @if($preorder->lapin_kg > 0)
                <tr>
                    <td>Lapin</td>
                    <td>{{ $preorder->lapin_kg }} kg</td>
                </tr>
                @endif
                @if($preorder->poulet_goliath_kg > 0)
                <tr>
                    <td>Poulet Goliath</td>
                    <td>{{ $preorder->poulet_goliath_kg }} kg</td>
                </tr>
                @endif
                <tr>
                    <td><strong>Total Général</strong></td>
                    <td><strong>{{ $preorder->total_kg }} kg</strong></td>
                </tr>
            </tbody>
        </table>

        <h2>Détails des livraisons planifiées</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Date souhaitée</th>
                    <th>Adresse</th>
                </tr>
            </thead>
            <tbody>
                @foreach($preorder->deliveries as $delivery)
                <tr>
                    <td>{{ ucfirst(str_replace('_', ' ', $delivery['productType'])) }}</td>
                    <td>{{ $delivery['quantityKg'] }} kg</td>
                    <td>{{ \Carbon\Carbon::parse($delivery['deliveryDate'])->format('d/m/Y') }}</td>
                    <td>{{ $delivery['deliveryAddress'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($preorder->project_note)
        <h2>Votre message / précision</h2>
        <p style="background: #f5f5f5; padding: 15px; border-radius: 6px; font-style: italic;">"{{ $preorder->project_note }}"</p>
        @endif

        <div class="footer">
            <p><strong>Ferme de l'Amitié</strong> - Engagement, Qualité et Transparence.</p>
            <p>Si vous avez des questions, n'hésitez pas à nous contacter via notre support WhatsApp ou en répondant à cet email.</p>
        </div>
    </div>
</body>
</html>
