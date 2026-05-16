<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nouvelle précommande (#{{ $preorder->id }})</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f6f8; }
        .container { max-width: 650px; margin: 30px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        h1 { color: #1a202c; font-size: 22px; margin-bottom: 20px; border-bottom: 2px solid #edf2f7; padding-bottom: 10px; }
        h2 { color: #2d3748; font-size: 18px; margin-top: 25px; margin-bottom: 15px; }
        p { margin-bottom: 15px; font-size: 15px; }
        .grid { display: flex; flex-wrap: wrap; margin-bottom: 20px; background: #f7fafc; padding: 20px; border-radius: 8px; }
        .grid-item { width: 50%; margin-bottom: 15px; }
        .grid-item label { font-size: 13px; color: #718096; text-transform: uppercase; font-weight: 600; display: block; margin-bottom: 4px; }
        .grid-item .value { font-size: 16px; font-weight: 500; color: #2d3748; }
        .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table th, .table td { padding: 12px 15px; border-bottom: 1px solid #edf2f7; text-align: left; font-size: 15px; }
        .table th { background-color: #f7fafc; color: #4a5568; font-weight: 600; }
        .badge { display: inline-block; padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 13px; }
        .badge.paid { background-color: #c6f6d5; color: #22543d; }
        .badge.pending { background-color: #feebc8; color: #7b341e; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #edf2f7; font-size: 13px; color: #a0aec0; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Nouvelle Précommande Enregistrée (#{{ $preorder->id }})</h1>
        
        <p>Une nouvelle demande de réservation a été effectuée sur le site de la Ferme de l'Amitié.</p>

        <div class="grid">
            <div class="grid-item">
                <label>Client</label>
                <div class="value">{{ $preorder->first_name }} {{ $preorder->last_name }}</div>
            </div>
            <div class="grid-item">
                <label>Email</label>
                <div class="value"><a href="mailto:{{ $preorder->email }}">{{ $preorder->email }}</a></div>
            </div>
            <div class="grid-item">
                <label>Téléphone</label>
                <div class="value">{{ $preorder->phone ?: 'Non renseigné' }}</div>
            </div>
            <div class="grid-item">
                <label>Statut Paiement</label>
                <div class="value">
                    @if($preorder->status === 'paid')
                        <span class="badge paid">Acompte Payé ({{ number_format($preorder->optional_prepayment, 0, ',', ' ') }} FCFA)</span>
                    @else
                        <span class="badge pending">En attente (Acompte non payé)</span>
                    @endif
                </div>
            </div>
            <div class="grid-item" style="width: 100%;">
                <label>Date de soumission</label>
                <div class="value">{{ $preorder->submitted_at->format('d/m/Y H:i:s') }}</div>
            </div>
        </div>

        <h2>Récapitulatif des volumes réservés</h2>
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

        <h2>Détails des livraisons</h2>
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
        <h2>Note / Message du client</h2>
        <p style="background: #edf2f7; padding: 15px; border-radius: 6px; font-style: italic;">"{{ $preorder->project_note }}"</p>
        @endif

        <div class="footer">
            <p>Notification automatique générée par la plateforme Ferme de l'Amitié.</p>
        </div>
    </div>
</body>
</html>
