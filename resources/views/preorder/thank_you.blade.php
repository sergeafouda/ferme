<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merci pour votre commande - Ferme de l'Amitié</title>
    @vite(['resources/css/app.css', 'resources/css/preorder.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .thank-you-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #fdf8f7 0%, #f5ebe9 100%);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .thank-you-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(132, 33, 20, 0.08);
            max-width: 680px;
            width: 100%;
            padding: 48px;
            text-align: center;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(132, 33, 20, 0.1);
        }
        .success-badge {
            width: 88px;
            height: 88px;
            background: #e6f4ea;
            color: #137333;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 24px;
            box-shadow: 0 10px 20px rgba(19, 115, 51, 0.15);
        }
        h1.title {
            font-size: clamp(24px, 5vw, 36px) !important;
            font-weight: 800 !important;
            color: #2c1613 !important;
            margin-bottom: 16px !important;
            line-height: 1.2 !important;
            max-width: none !important;
            width: 100% !important;
            white-space: nowrap !important;
        }
        .subtitle {
            font-size: 18px;
            color: #666;
            margin-bottom: 32px;
            line-height: 1.6;
        }
        .order-box {
            background: #fdf8f7;
            border-radius: 16px;
            padding: 28px;
            text-align: left;
            margin-bottom: 32px;
            border: 1px solid rgba(132, 33, 20, 0.15);
        }
        .order-box h3 {
            font-size: 20px;
            font-weight: 700;
            color: #2c1613;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .status-pill {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 700;
        }
        .status-pill.paid {
            background: #e6f4ea;
            color: #137333;
        }
        .status-pill.pending {
            background: #fef7e0;
            color: #b06000;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }
        .info-item {
            background: #fff;
            padding: 16px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .info-label {
            font-size: 13px;
            color: #888;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .info-value {
            font-size: 16px;
            font-weight: 700;
            color: #333;
        }
        .action-row {
            display: flex;
            gap: 16px;
            justify-content: center;
        }
        .btn-home {
            background: linear-gradient(135deg, #c62828 0%, #db3f2f 100%) !important;
            color: #ffffff !important;
            padding: 20px 36px !important;
            border-radius: 999px !important;
            font-weight: 700 !important;
            font-size: 17px !important;
            text-decoration: none !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 16px 32px rgba(198, 40, 40, 0.25) !important;
            display: inline-block !important;
            border: none !important;
        }
        .btn-home:hover {
            transform: translateY(-4px) !important;
            box-shadow: 0 24px 48px rgba(198, 40, 40, 0.35) !important;
            color: #ffffff !important;
        }
        .btn-receipt {
            background: #fff !important;
            color: #c62828 !important;
            border: 2px solid #c62828 !important;
            padding: 18px 32px !important;
            border-radius: 999px !important;
            font-weight: 700 !important;
            font-size: 17px !important;
            text-decoration: none !important;
            transition: all 0.3s ease !important;
            display: inline-block !important;
        }
        .btn-receipt:hover {
            background: #fff4f2 !important;
            transform: translateY(-4px) !important;
            box-shadow: 0 12px 24px rgba(198, 40, 40, 0.15) !important;
        }
        .mail-notice {
            margin-top: 24px;
            font-size: 14px;
            color: #888;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        @media (max-width: 640px) {
            .thank-you-card { padding: 32px 20px; }
            .info-grid { grid-template-columns: 1fr; }
            .title { font-size: 26px; }
            .action-row { flex-direction: column; gap: 12px; }
        }
    </style>
</head>
<body>
    <div class="thank-you-wrap">
        <div class="thank-you-card">
            <div class="success-badge">✓</div>
            <h1 class="title">Merci pour votre commande !</h1>
            <p class="subtitle">Votre réservation a été enregistrée avec succès. Notre équipe a été notifiée et prépare l'organisation de vos volumes.</p>

            <div class="order-box">
                <h3>
                    <span>Commande #{{ $preorder->id }}</span>
                    @if($preorder->status === 'paid')
                        <span class="status-pill paid">Acompte Payé</span>
                    @else
                        <span class="status-pill pending">Paiement en attente</span>
                    @endif
                </h3>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Client</div>
                        <div class="info-value">{{ $preorder->first_name }} {{ $preorder->last_name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Quantité Totale</div>
                        <div class="info-value">{{ $preorder->total_kg }} kg</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Acompte de sécurisation</div>
                        <div class="info-value">{{ number_format($preorder->optional_prepayment, 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Statut de priorité</div>
                        <div class="info-value" style="color: {{ $preorder->status === 'paid' ? '#137333' : '#b06000' }};">
                            {{ $preorder->status === 'paid' ? 'Priorité Absolue' : 'Standard' }}
                        </div>
                    </div>
                </div>

                @if($preorder->status !== 'paid')
                <div style="background: #fff; padding: 16px; border-radius: 12px; border-left: 4px solid #b06000; font-size: 14px; color: #666; margin-top: 16px;">
                    <strong>Information :</strong> Vous avez choisi de conserver votre réservation sans régler l'acompte pour le moment. Vous pourrez être recontacté ultérieurement par notre équipe pour finaliser la confirmation.
                </div>
                @endif
            </div>

            <div class="action-row">
                <a href="{{ url('/') }}" class="btn-home">Retour à l'accueil</a>
                @if($preorder->receipt_url)
                    <a href="{{ $preorder->receipt_url }}" target="_blank" class="btn-receipt">📄 Télécharger ma facture PDF</a>
                @endif
            </div>

            <div class="mail-notice">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                <span>Un email de confirmation vous a été envoyé à {{ $preorder->email }}</span>
            </div>
        </div>
    </div>
</body>
</html>
