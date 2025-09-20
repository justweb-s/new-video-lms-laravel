@extends('layouts.public')

@push('styles')
<style>
    .hero-section {
        background-image: url('/images/percorso-workout-personalizzato.jpg');
        background-size: cover;
        background-position: center top;
        background-repeat: no-repeat;
        background-attachment: fixed;
        position: relative;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    @media (max-width: 768px) {
        .hero-section {
            background-attachment: scroll;
            min-height: 80vh;
        }
    }
    
    .hero-overlay {
        background: rgba(0, 0, 0, 0.4);
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
    }
    
    .hero-content {
        position: relative;
        z-index: 2;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
    }
    
    .yellow-banner {
        background-color: #f6e849;
        color: #36583d;
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 1rem 2rem;
        border-radius: 4px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        font-size: 1.1rem;
        line-height: 1.3;
        max-width: 90%;
    }
    
    @media (max-width: 768px) {
        .yellow-banner {
            font-size: 0.95rem;
            padding: 0.8rem 1.5rem;
            letter-spacing: 0.5px;
        }
    }
    
    .section-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        color: #36583d;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 2.2rem;
        margin-bottom: 2rem;
        text-align: center;
    }
    
    @media (max-width: 768px) {
        .section-title {
            font-size: 1.8rem;
        }
    }
    
    @media (max-width: 480px) {
        .section-title {
            font-size: 1.5rem;
        }
    }
    
    .section-spacing {
        padding: 4rem 0;
    }
    
    @media (max-width: 768px) {
        .section-spacing {
            padding: 3rem 0;
        }
    }
    
    @media (max-width: 480px) {
        .section-spacing {
            padding: 2rem 0;
        }
    }
    
    .container-responsive {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }
    
    @media (min-width: 640px) {
        .container-responsive {
            padding: 0 1.5rem;
        }
    }
    
    @media (min-width: 1024px) {
        .container-responsive {
            padding: 0 2rem;
        }
    }
    
    .body-text {
        font-family: 'Source Sans Pro', sans-serif;
        font-size: 1.1rem;
        line-height: 1.6;
        color: #555;
        margin-bottom: 1.5rem;
    }
    
    .highlight-section {
        background-color: #f6e849;
        color: #36583d;
        padding: 2rem;
        border-radius: 12px;
        margin: 3rem 0;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        text-align: center;
    }
    
    .highlight-section p {
        font-family: 'Source Sans Pro', sans-serif;
        font-size: 1.2rem;
        line-height: 1.5;
        font-weight: 600;
        margin: 0;
    }
    
    .timeline-container {
        position: relative;
        max-width: 800px;
        margin: 0 auto;
    }
    
    .timeline-container::before {
        content: '';
        position: absolute;
        left: 30px;
        top: 80px;
        bottom: 80px;
        width: 3px;
        background-color: #f6e849;
        z-index: 1;
    }
    
    @media (max-width: 768px) {
        .timeline-container::before {
            left: 25px;
        }
    }
    
    .step-section {
        position: relative;
        margin-bottom: 4rem;
        padding-left: 100px;
        padding-bottom: 2rem;
    }
    
    @media (max-width: 768px) {
        .step-section {
            padding-left: 80px;
            margin-bottom: 3rem;
        }
    }
    
    .step-section:last-child {
        margin-bottom: 0;
    }
    
    .step-number {
        position: absolute;
        left: 0;
        top: 0;
        width: 60px;
        height: 60px;
        background-color: #f6e849;
        color: #36583d;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Montserrat', sans-serif;
        font-weight: 800;
        font-size: 1.5rem;
        box-shadow: 0 4px 15px rgba(246, 232, 73, 0.4);
        z-index: 2;
    }
    
    @media (max-width: 768px) {
        .step-number {
            width: 50px;
            height: 50px;
            font-size: 1.3rem;
        }
    }
    
    .step-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        color: #36583d;
        font-size: 1.4rem;
        margin-bottom: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    @media (max-width: 768px) {
        .step-title {
            font-size: 1.2rem;
        }
    }
    
    .step-list {
        list-style: none;
        padding: 0;
        margin: 1.5rem 0;
    }
    
    .step-list li {
        position: relative;
        padding-left: 1.5rem;
        margin-bottom: 0.75rem;
        font-family: 'Source Sans Pro', sans-serif;
        font-size: 1rem;
        line-height: 1.6;
        color: #555;
    }
    
    .step-list li::before {
        content: "•";
        color: #36583d;
        font-weight: bold;
        position: absolute;
        left: 0;
        top: 0;
    }
    
    .step-list li strong {
        color: #36583d;
        font-weight: 600;
    }
    
    .cta-section {
        background-color: #36583d;
        padding: 3rem 0;
        text-align: center;
        margin-top: 4rem;
    }
    
    .cta-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        color: #f6e849;
        font-size: 2rem;
        text-transform: uppercase;
        margin-bottom: 2rem;
        letter-spacing: 1px;
    }
    
    @media (max-width: 768px) {
        .cta-title {
            font-size: 1.5rem;
        }
    }
    
    .cta-buttons {
        display: flex;
        justify-content: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .cta-btn {
        background-color: #f6e849;
        color: #36583d;
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        text-transform: uppercase;
        padding: 1rem 2rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        font-size: 0.9rem;
        letter-spacing: 0.5px;
    }
    
    .cta-btn:hover {
        background-color: #f4e030;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }
    
    @media (max-width: 480px) {
        .cta-buttons {
            flex-direction: column;
            align-items: center;
        }
        
        .cta-btn {
            width: 100%;
            max-width: 280px;
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<div class="hero-section">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="yellow-banner">
            PERCORSO DI ALLENAMENTO ADATTATO PERSONALIZZATO
        </div>
    </div>
</div>

<!-- Main Content Section -->
<div class="bg-white section-spacing">
    <div class="container-responsive">
        <div class="text-center mb-8 md:mb-12 lg:mb-16">
            <h2 class="section-title">IL TUO PERCORSO PERSONALIZZATO</h2>
            <div class="w-16 md:w-24 h-1 bg-gray-300 mx-auto"></div>
        </div>

        <div class="highlight-section">
            <p>
                Ogni percorso di coaching è unico e progettato in base alle tue esigenze. Il mio approccio, sia online che in presenza, è strutturato in diverse tappe, per garantire che tu possa raggiungere i tuoi obiettivi in modo chiaro, progressivo e sostenibile.
            </p>
        </div>

        <div class="timeline-container">
            <div class="step-section">
                <div class="step-number">1</div>
                <h3 class="step-title">Consulenza Iniziale e Valutazione</h3>
                <p class="body-text">
                    Questa è una sessione introduttiva in cui ci conosceremo e discuteremo dei tuoi obiettivi, delle tue aspettative e delle tue attuali condizioni fisiche e mentali. Per i programmi personalizzati online, la consulenza sarà effettuata in call, permettendoci di stabilire un contatto diretto anche a distanza. Questa fase è fondamentale per identificare il punto di partenza e valutare eventuali problematiche o limitazioni.
                </p>
                <ul class="step-list">
                    <li><strong>Obiettivi:</strong> Definire chiaramente i tuoi obiettivi a breve e lungo termine.</li>
                    <li><strong>Valutazione iniziale:</strong> Analisi delle tue abitudini, stile di vita, eventuali problematiche fisiche o personali.</li>
                </ul>
            </div>

            <div class="step-section">
                <div class="step-number">2</div>
                <h3 class="step-title">Pianificazione Personalizzata</h3>
                <p class="body-text">
                    Dopo la consulenza iniziale, elaborerò un <strong style="color: #36583d;">piano personalizzato</strong> specifico per le tue esigenze, che verrà consegnato entro 6 giorni lavorativi. Questo può includere allenamenti, strategie di benessere mentale, consigli nutrizionali. Il piano sarà dettagliato ma flessibile, adattandosi al tuo ritmo.
                </p>
                <ul class="step-list">
                    <li><strong>Piano di azione:</strong> Creazione di un programma di allenamento su misura, con step settimanali o mensili.</li>
                    <li><strong>Strumenti e risorse:</strong> Condivisione dei video con l'esecuzione di ogni singolo esercizio per supportarti durante il percorso.</li>
                </ul>
            </div>

            <div class="step-section">
                <div class="step-number">3</div>
                <h3 class="step-title">Adattamento e Ottimizzazione</h3>
                <p class="body-text">
                    Durante il percorso, potresti affrontare cambiamenti o scoprire nuove esigenze. In questa fase ci concentriamo su:
                </p>
                <ul class="step-list">
                    <li><strong>Riadattamento:</strong> Se necessario, modificheremo il piano in base ai tuoi progressi, alle nuove difficoltà o ad eventuali cambi di obiettivo.</li>
                    <li><strong>Ottimizzazione del percorso:</strong> Introduzione di nuove tecniche o esercizi per massimizzare i risultati.</li>
                </ul>
            </div>

            <div class="step-section">
                <div class="step-number">4</div>
                <h3 class="step-title">Supporto e Feedback</h3>
                <p class="body-text">
                    Il coaching online ti consente di avere <strong style="color: #36583d;">supporto costante anche tra le sessioni</strong>. Potrai contattarmi tramite messaggi o email per domande, dubbi o per ricevere un incoraggiamento. Questo ti aiuta a mantenere alta la motivazione e a sentirti sempre seguito nel tuo percorso. La comunicazione è importante, e lo scambio di informazioni tramite FEEDBACK e CHECK settimanali sono fondamentali per un percorso valido che abbia risultati sperati.
                </p>
                <ul class="step-list">
                    <li><strong>Monitoraggio dei progressi:</strong> Revisione dei risultati ottenuti rispetto agli obiettivi stabiliti.</li>
                    <li><strong>Feedback e aggiustamenti:</strong> Apporto di eventuali modifiche al piano, per adattarlo alle tue esigenze e garantire continui progressi.</li>
                    <li><strong>Supporto continuo:</strong> Motivazione e guida per affrontare eventuali sfide o difficoltà.</li>
                </ul>
                <p class="body-text">Le sessioni sono programmate con una frequenza settimanale, in base alle tue necessità e al tipo di percorso.</p>
            </div>

            <div class="step-section">
                <div class="step-number">5</div>
                <h3 class="step-title">Percorso Continuativo o Evoluzione</h3>
                <p class="body-text">
                    Al termine del percorso, potrai decidere di continuare con un <strong style="color: #36583d;">percorso di coaching continuativo</strong>, per lavorare su nuovi obiettivi.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="cta-section">
    <div class="container-responsive">
        <h2 class="cta-title">Inizia il tuo percorso di trasformazione</h2>
        <div class="cta-buttons">
            <a href="{{ route('static.workout-in-studio') }}" class="cta-btn">
                Allenamento adattato in studio
            </a>
            <a href="{{ route('static.workout-online') }}" class="cta-btn">
                Allenamento adattato online
            </a>
        </div>
    </div>
</div>
@endsection