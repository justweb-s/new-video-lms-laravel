@extends('layouts.public')

@push('styles')
<style>
    .workout-studio-hero {
        background-image: url('/images/hero-workout-studio.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    
    /* Mobile optimization for hero */
    @media (max-width: 768px) {
        .workout-studio-hero {
            background-attachment: scroll;
            min-height: 80vh;
        }
    }

    .workout-studio-hero::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.4);
        z-index: 1;
    }
    
    .workout-studio-hero .container-responsive {
        position: relative;
        z-index: 2;
    }

    .workout-studio-hero h1 {
        font-family: 'Montserrat', sans-serif;
        font-size: 3.5rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 20px;
        position: relative;
        z-index: 2;
        text-shadow: 2px 2px 8px rgba(0,0,0,0.6);
    }
    
    .workout-studio-hero .highlight {
        background: #36583d;
        color: #f6e849;
        padding: 5px 15px;
        border-radius: 25px;
        display: inline-block;
    }
    

    
    .logo-section {
        padding: 40px 0;
        background: #fff;
        text-align: center;
    }
    
    .logo-section img {
        width: 120px;
        height: 120px;
        margin: 0 auto 30px;
        border-radius: 50%;
        box-shadow: 0 10px 30px rgba(54, 88, 61, 0.2);
    }
    
    .intro-section {
        padding: 80px 0;
        background: #fff;
    }
    
    .intro-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        text-align: center;
    }
    
    .intro-title {
        font-family: 'Montserrat', sans-serif;
        font-size: 2.5rem;
        color: #36583d;
        margin-bottom: 30px;
        font-weight: 700;
    }
    
    .intro-subtitle {
        font-family: 'Source Sans Pro', sans-serif;
        font-size: 1.3rem;
        color: #333;
        font-style: italic;
        font-weight: 700;
        margin-bottom: 40px;
        line-height: 1.6;
    }
    
    .feature-box {
        background: rgba(246, 232, 73, 0.55);
        padding: 40px;
        border-radius: 20px;
        text-align: center;
        max-width: 800px;
        margin: 40px auto;
        box-shadow: 0 10px 30px rgba(54, 88, 61, 0.1);
    }
    
    .feature-title {
        font-family: 'Montserrat', sans-serif;
        color: #36583d;
        font-size: 1.8rem;
        margin-bottom: 20px;
        font-weight: 700;
    }
    
    .feature-text {
        font-family: 'Source Sans Pro', sans-serif;
        color: #36583d;
        line-height: 1.6;
        font-size: 1.1rem;
        margin: 0;
    }
    
    .feature-text strong {
        font-weight: 700;
    }
    
    .content-section {
        padding: 80px 0;
        background: #f8f9fa;
    }
    
    .content-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .section-title {
        font-family: 'Montserrat', sans-serif;
        font-size: 2.5rem;
        color: #36583d;
        margin-bottom: 20px;
        position: relative;
    }
    
    .section-title::after {
        content: '';
        display: block;
        width: 60px;
        height: 4px;
        background: #f6e849;
        margin-top: 15px;
    }
    
    .section-text {
        font-family: 'Source Sans Pro', sans-serif;
        font-size: 1.1rem;
        line-height: 1.8;
        color: #333;
        margin-bottom: 30px;
    }
    
    .cta-button {
        display: inline-block;
        background: #36583d;
        color: #f6e849;
        padding: 15px 30px;
        border-radius: 25px;
        text-decoration: none;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        margin: 20px 0;
    }
    
    .cta-button:hover {
        background: #2a4530;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(54, 88, 61, 0.3);
        color: #f6e849;
        text-decoration: none;
    }
    
    .image-section {
        text-align: center;
        margin: 60px 0;
    }
    
    .image-section img {
        width: 100%;
        max-width: 800px;
        height: auto;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(54, 88, 61, 0.2);
    }
    
    .two-column-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
        margin: 80px 0;
    }
    
    .ambiente-section {
        background: #36583d;
        color: #f6e849;
        padding: 80px 0;
        position: relative;
        overflow: hidden;
    }
    
    .ambiente-section::before,
    .ambiente-section::after {
        content: '';
        position: absolute;
        background: rgba(246, 232, 73, 0.1);
        border-radius: 50%;
    }
    
    .ambiente-section::before {
        top: -50px;
        left: -50px;
        width: 200px;
        height: 200px;
    }
    
    .ambiente-section::after {
        bottom: -100px;
        right: -100px;
        width: 300px;
        height: 300px;
    }
    
    .ambiente-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        position: relative;
        z-index: 1;
    }
    
    .ambiente-title {
        font-family: 'Montserrat', sans-serif;
        font-size: 2.5rem;
        color: #fff;
        margin-bottom: 20px;
        position: relative;
    }
    
    .ambiente-title::after {
        content: '';
        display: block;
        width: 60px;
        height: 4px;
        background: #f6e849;
        margin-top: 15px;
    }
    
    .ambiente-text {
        font-family: 'Source Sans Pro', sans-serif;
        font-size: 1.2rem;
        line-height: 1.8;
        margin-bottom: 40px;
    }
    
    .consulenza-section {
        background: linear-gradient(135deg, #f6e849 0%, #f4e030 100%);
        padding: 80px 0;
    }
    
    .consulenza-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
    }
    
    .consulenza-image {
        position: relative;
    }
    
    .consulenza-image img {
        width: 100%;
        height: auto;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(54, 88, 61, 0.2);
    }
    
    .consulenza-text {
        text-align: center;
    }
    
    .consulenza-title {
        font-family: 'Montserrat', sans-serif;
        font-size: 2.2rem;
        color: #36583d;
        margin-bottom: 30px;
        line-height: 1.3;
        font-weight: 700;
    }
    
    .consulenza-title::after {
        content: '';
        display: block;
        width: 60px;
        height: 4px;
        background: #36583d;
        margin: 20px auto;
    }
    
    .cta-buttons {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 30px;
    }
    
    .cta-button-whatsapp {
        background: #25D366;
        color: white;
    }
    
    .cta-button-whatsapp:hover {
        background: #128C7E;
        color: white;
    }
    
    .cta-button-email {
        background: #36583d;
        color: #f6e849;
    }
    
    .cta-button-email:hover {
        background: #2a4530;
        color: #f6e849;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }
    
    @media (max-width: 768px) {
        .workout-studio-hero h1 {
            font-size: 2.5rem;
        }
        
        .workout-studio-hero .subtitle {
            font-size: 1.2rem;
        }
        
        .two-column-section,
        .consulenza-content {
            grid-template-columns: 1fr;
            gap: 40px;
        }
        
        .section-title,
        .intro-title {
            font-size: 2rem;
        }
        
        .ambiente-title,
        .consulenza-title {
            font-size: 2rem;
        }
        
        .cta-buttons {
            flex-direction: column;
            align-items: center;
        }
        
        .cta-button {
            width: 100%;
            max-width: 300px;
            text-align: center;
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<div class="workout-studio-hero">
    <div class="container-responsive">
        <h1><span class="highlight">Workout in Studio</span></h1>
    </div>
</div>

<!-- Logo Section -->
<div class="logo-section">
    <img src="/images/favicon-studio.png" alt="EMY Workout Logo" loading="lazy">
    <div class="intro-content">
        <h2 class="intro-title">Allenamento in presenza</h2>
        <p class="intro-subtitle">Raggiungi i tuoi obiettivi con un programma di allenamento completamente personalizzato e l'attenzione esclusiva a ogni dettaglio.</p>
    </div>
</div>

<!-- Feature Box -->
<div class="feature-box">
    <h3 class="feature-title">PERSONALIZZAZIONE ASSOLUTA</h3>
    <p class="feature-text">Ogni programma è <strong>creato e adattato</strong> sulle tue caratteristiche uniche, garantendo un allenamento one-to-one che rispetta le tue esigenze specifiche e ottimizza i risultati.</p>
</div>

<!-- Content Section -->
<div class="content-section">
    <div class="content-wrapper">
        <h2 class="section-title">Il Tuo Percorso è il Tuo Obbiettivo</h2>
        <p class="section-text">Benvenuto nello studio Emy Workout, dove ogni programma è creato su misura per le tue esigenze specifiche. Qui, l'attenzione è rivolta esclusivamente a te: allenamento one-to-one pensato per migliorare il tuo benessere fisico, tenendo conto delle tue caratteristiche uniche e delle eventuali problematiche fisiche o di salute.</p>
        <a href="#consulenza" class="cta-button">Prenota una consulenza</a>
        
        <div class="image-section">
            <img src="/images/allenamento5.jpg" alt="Allenamento personalizzato in studio" loading="lazy">
        </div>
        
        <div class="two-column-section">
            <div>
                <img src="/images/ambiente-sicuro-professionale.jpg" alt="Ambiente sicuro e professionale" loading="lazy" style="width: 100%; border-radius: 20px; box-shadow: 0 20px 40px rgba(54, 88, 61, 0.2);">
            </div>
            <div>
                <h2 class="section-title">Un approccio su misura</h2>
                <p class="section-text">Ogni persona è diversa e così deve essere il suo allenamento. Nel mio studio, l'allenamento adattato è personalizzato in base alle tue condizioni fisiche e alle tue esigenze personali, che possono includere condizioni mediche, limitazioni fisiche o differenze individuali.</p>
            </div>
        </div>
    </div>
</div>

<!-- Ambiente Section -->
<div class="ambiente-section">
    <div class="ambiente-content">
        <h2 class="ambiente-title">Ambiente Sicuro e Professionale</h2>
        <p class="ambiente-text">Allenati in un ambiente sicuro e professionale, dove ogni sessione è pianificata per garantirti il massimo comfort e i migliori risultati. L'uso di attrezzature moderne e tecniche avanzate rende il percorso divertente, motivante e sicuro.</p>
        
        <div class="image-section">
            <img src="/images/studio2.jpg" alt="Studio professionale EMY Workout" loading="lazy">
        </div>
    </div>
</div>

<!-- Consulenza Section -->
<div id="consulenza" class="consulenza-section">
    <div class="consulenza-content">
        <div class="consulenza-image">
            <img src="/images/consulenza.jpg" alt="Consulenza personalizzata" loading="lazy">
        </div>
        <div class="consulenza-text">
            <h2 class="consulenza-title">Prenota la tua consulenza gratuita e inizia il tuo percorso verso il massimo del tuo potenziale.</h2>
            <div class="cta-buttons">
                <a href="{{ route('static.contact') }}" class="cta-button cta-button-whatsapp">Prenota tramite WhatsApp</a>
                <a href="{{ route('static.contact') }}" class="cta-button cta-button-email">Prenota tramite Email</a>
            </div>
        </div>
    </div>
</div>
@endsection
