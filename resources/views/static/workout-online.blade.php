@extends('layouts.public')

@push('styles')
<style>
    .workout-online-hero {
        background-image: url('/images/hero-workout-online.jpg');
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
        .workout-online-hero {
            background-attachment: scroll;
            min-height: 80vh;
        }
    }

    .workout-online-hero::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.4);
        z-index: 1;
    }
    

    
    .workout-online-hero .container-responsive {
        position: relative;
        z-index: 2;
    }

    .workout-online-hero h1 {
        font-family: 'Montserrat', sans-serif;
        font-size: 3.5rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 20px;
        position: relative;
        z-index: 2;
        text-shadow: 2px 2px 8px rgba(0,0,0,0.6);
    }
    
    .workout-online-hero .highlight {
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
        padding: 60px 0;
        background: #fff;
    }
    
    .intro-content {
        max-width: 1000px;
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
    
    .intro-text {
        font-family: 'Source Sans Pro', sans-serif;
        font-size: 1.2rem;
        color: #333;
        line-height: 1.8;
        margin-bottom: 20px;
    }
    
    .intro-text strong {
        color: #36583d;
        font-weight: 600;
    }
    
    .training-section {
        position: relative;
        padding: 80px 0;
        background: rgba(246, 232, 73, 0.75);
        overflow: hidden;
    }
    
    .training-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        z-index: 2;
    }
    
    .training-content {
        flex: 1;
        padding-right: 50px;
    }
    
    .training-title {
        color: #2F5233;
        font-family: 'Montserrat', sans-serif;
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 30px;
        line-height: 1.2;
    }
    
    .training-intro {
        color: #2F5233;
        font-family: 'Source Sans Pro', sans-serif;
        font-size: 1.3rem;
        line-height: 1.6;
        margin-bottom: 40px;
        font-weight: 500;
    }
    
    .training-points {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .training-points li {
        color: #2F5233;
        font-family: 'Montserrat', sans-serif;
        font-size: 1.1rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }
    
    .training-points li::before {
        content: '';
        width: 12px;
        height: 12px;
        background-color: #2F5233;
        border-radius: 50%;
        margin-right: 15px;
    }
    
    .training-closing {
        color: #2F5233;
        font-family: 'Source Sans Pro', sans-serif;
        font-size: 1.3rem;
        margin-top: 40px;
        font-style: italic;
        font-weight: 500;
    }
    
    .training-image {
        flex: 0 0 45%;
        padding: 20px;
    }
    
    .training-image img {
        width: 100%;
        border-radius: 15px;
        border: 4px solid rgba(47, 82, 51, 0.2);
        box-shadow: 0 20px 40px rgba(54, 88, 61, 0.2);
    }
    
    /* Program Cards Styles - Same as Home Page */
    .section-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        color: #36583d;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    /* Responsive section title */
    @media (max-width: 768px) {
        .section-title {
            font-size: 1.8rem !important;
        }
    }
    
    @media (max-width: 480px) {
        .section-title {
            font-size: 1.5rem !important;
        }
    }
    
    .program-card {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        height: 400px;
        background-size: cover;
        background-position: center top;
        background-repeat: no-repeat;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .program-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }
    
    /* Responsive program cards */
    @media (max-width: 768px) {
        .program-card {
            height: 380px;
            border-radius: 8px;
            background-position: center center;
        }
    }
    
    @media (max-width: 480px) {
        .program-card {
            height: 350px;
            background-position: center top;
            background-size: cover;
        }
    }
    
    /* Specific adjustments for vertical images on mobile */
    @media (max-width: 480px) {
        .program-card {
            aspect-ratio: 4/5; /* More vertical ratio for mobile */
            height: auto;
            min-height: 320px;
        }
        
        /* Specific positioning for each image type */
        .program-card[style*="burn-fit.jpg"] {
            background-position: center 20%; /* Focus on upper body */
        }
        
        .program-card[style*="booty-boost.jpg"] {
            background-position: center 30%; /* Focus on middle area */
        }
        
        .program-card[style*="sculpt-fit.jpg"] {
            background-position: center 25%; /* Focus on upper-middle */
        }
        
        .program-card[style*="personalizzato.jpg"] {
            background-position: center 35%; /* Adjust based on image content */
        }
    }
    
    /* Additional tablet optimizations */
    @media (min-width: 481px) and (max-width: 768px) {
        .program-card {
            aspect-ratio: 3/4; /* Slightly more vertical for tablet */
        }
    }
    
    .program-overlay {
        background: linear-gradient(to bottom, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.8) 100%);
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
    }
    
    .program-content {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 1.5rem;
        color: white;
    }
    
    /* Responsive program content */
    @media (max-width: 480px) {
        .program-content {
            padding: 1rem;
        }
    }
    
    .program-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 800;
        font-size: 1.4rem;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }
    
    /* Responsive program title */
    @media (max-width: 768px) {
        .program-title {
            font-size: 1.2rem;
        }
    }
    
    @media (max-width: 480px) {
        .program-title {
            font-size: 1.1rem;
        }
    }
    
    .program-subtitle {
        font-family: 'Source Sans Pro', sans-serif;
        font-weight: 400;
        margin-bottom: 0.25rem;
        font-size: 1rem;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }
    
    .program-duration {
        font-family: 'Source Sans Pro', sans-serif;
        font-weight: 300;
        font-size: 0.9rem;
        margin-bottom: 1rem;
        opacity: 0.9;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }
    
    .program-btn {
        background-color: #36583d;
        color: white;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        text-transform: uppercase;
        padding: 0.7rem 1.2rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }
    
    .program-btn:hover {
        background-color: #2a4530;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.4);
    }
    
    /* Responsive button */
    @media (max-width: 480px) {
        .program-btn {
            padding: 0.6rem 1rem;
            font-size: 0.75rem;
        }
    }
    
    /* Grid responsive improvements */
    .programs-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr); /* 4 columns on desktop */
        gap: 1.5rem;
    }
    
    /* Large desktop - ensure 4 columns with proper spacing */
    @media (min-width: 1200px) {
        .programs-grid {
            gap: 2rem;
        }
    }
    
    /* Tablet - 2 columns */
    @media (max-width: 1023px) {
        .programs-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
    }
    
    /* Small tablet */
    @media (max-width: 768px) {
        .programs-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
    }
    
    /* Mobile - 1 column */
    @media (max-width: 480px) {
        .programs-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }
    
    /* Section spacing responsive */
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
    
    /* Container responsive */
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
    
    .courses-section {
        padding: 80px 0;
        background: #fff;
    }
    
    .courses-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .section-title {
        font-family: 'Montserrat', sans-serif;
        font-size: 2.5rem;
        color: #36583d;
        text-align: center;
        margin-bottom: 20px;
        position: relative;
    }
    
    .section-title::after {
        content: '';
        display: block;
        width: 60px;
        height: 4px;
        background: #f6e849;
        margin: 20px auto;
    }
    
    .courses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 40px;
    }
    
    .course-card {
        background: #fff;
        border-radius: 15px;
        padding: 25px;
        border: 2px solid #f0f0f0;
        transition: all 0.3s ease;
    }
    
    .course-card:hover {
        border-color: #f6e849;
        box-shadow: 0 10px 30px rgba(54, 88, 61, 0.1);
        transform: translateY(-2px);
    }
    
    .course-title {
        font-family: 'Montserrat', sans-serif;
        font-size: 1.3rem;
        color: #36583d;
        margin-bottom: 15px;
        font-weight: 600;
    }
    
    .course-description {
        font-family: 'Source Sans Pro', sans-serif;
        color: #666;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 20px;
    }
    
    .course-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .course-price {
        font-family: 'Montserrat', sans-serif;
        color: #36583d;
        font-weight: 700;
        font-size: 1.2rem;
    }
    
    .course-button {
        background: #36583d;
        color: #f6e849;
        padding: 8px 20px;
        border-radius: 20px;
        text-decoration: none;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }
    
    .course-button:hover {
        background: #2a4530;
        color: #f6e849;
        text-decoration: none;
        transform: translateY(-1px);
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }
    
    @media (max-width: 768px) {
        .workout-online-hero h1 {
            font-size: 2.5rem;
        }
        
        .training-container {
            flex-direction: column;
        }
        
        .training-content {
            padding-right: 0;
            margin-bottom: 40px;
        }
        
        .training-image {
            width: 100%;
        }
        
        .training-title {
            font-size: 2.2rem;
        }
        
        .programs-grid {
            grid-template-columns: 1fr;
        }
        
        .courses-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<div class="workout-online-hero">
    <div class="container-responsive">
        <h1><span class="highlight">Workout Online</span></h1>
    </div>
</div>

<!-- Logo Section -->
<div class="logo-section">
    <img src="/images/favicon-studio.png" alt="EMY Workout Logo" loading="lazy">
    <div class="intro-content">
        <h2 class="intro-title">Allenati ovunque tu sia con il mio workout online!</h2>
        <p class="intro-text">Il mio servizio di workout online ti offre la libertà di allenarti dove preferisci: <strong><em>a casa, in palestra,</em></strong> o in qualsiasi altro luogo ti sia più comodo.</p>
        <p class="intro-text">Con programmi strutturati e sessioni brevi e mirate, potrai raggiungere i tuoi obiettivi in modo semplice ed efficace.</p>
    </div>
</div>

<!-- Training Section -->
<section class="training-section">
    <div class="training-container">
        <div class="training-content">
            <h1 class="training-title">RAGGIUNGI IL TUO POTENZIALE</h1>
            
            <p class="training-intro">
                Non importa dove ti trovi, ciò che conta è come ti alleni. Il mio approccio si basa su tre concetti fondamentali:
            </p>
            
            <ul class="training-points">
                <li>INTENSITÀ MIRATA</li>
                <li>FREQUENZA OTTIMIZZATA</li>
                <li>PRECISIONE NELL'ESECUZIONE</li>
            </ul>
            
            <p class="training-closing">
                Questi elementi chiave ti guideranno verso i risultati che hai sempre sognato.
            </p>
        </div>
        
        <div class="training-image">
            <img src="/images/emy-new.jpg" alt="Personal Trainer Emy" loading="lazy">
        </div>
    </div>
</section>


<!-- Allenamento Adattato Section -->
<div class="bg-white section-spacing">
    <div class="container-responsive">
        <div class="text-center mb-8 md:mb-12 lg:mb-16">
            <h2 class="section-title text-2xl md:text-3xl mb-4">ALLENAMENTO ADATTATO</h2>
            <div class="w-16 md:w-24 h-1 bg-gray-300 mx-auto"></div>
        </div>
        
        <!-- Programs Grid -->
        <div class="programs-grid">
            <!-- Dimagrimento Card -->
            <div class="program-card" style="background-image: url('/images/burn-fit.jpg')">
                <div class="program-overlay"></div>
                <div class="program-content">
                    <h3 class="program-title">DIMAGRIMENTO</h3>
                    <p class="program-subtitle">Burn Fit</p>
                    <p class="program-duration">30 Giorni</p>
                    <a href="{{ route('catalog.index') }}" class="program-btn">
                        SCOPRI DI PIÙ →
                    </a>
                </div>
            </div>
            
            <!-- Glutei Card -->
            <div class="program-card" style="background-image: url('/images/booty-boost.jpg')">
                <div class="program-overlay"></div>
                <div class="program-content">
                    <h3 class="program-title">GLUTEI</h3>
                    <p class="program-subtitle">Booty Boost</p>
                    <p class="program-duration">30 Giorni</p>
                    <a href="{{ route('catalog.index') }}" class="program-btn">
                        SCOPRI DI PIÙ →
                    </a>
                </div>
            </div>
            
            <!-- Tonificazione Card -->
            <div class="program-card" style="background-image: url('/images/sculpt-fit.jpg')">
                <div class="program-overlay"></div>
                <div class="program-content">
                    <h3 class="program-title">TONIFICAZIONE</h3>
                    <p class="program-subtitle">Sculpt Fit</p>
                    <p class="program-duration">30 Giorni</p>
                    <a href="{{ route('catalog.index') }}" class="program-btn">
                        SCOPRI DI PIÙ →
                    </a>
                </div>
            </div>
            
            <!-- Personalizzato Card -->
            <div class="program-card" style="background-image: url('/images/personalizzato.jpg')">
                <div class="program-overlay"></div>
                <div class="program-content">
                    <h3 class="program-title">PERSONALIZZATO</h3>
                    <p class="program-subtitle">Allenamento Personalizzato</p>
                    <p class="program-duration">30 Giorni</p>
                    <a href="{{ route('static.contact') }}" class="program-btn">
                        SCOPRI DI PIÙ →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
