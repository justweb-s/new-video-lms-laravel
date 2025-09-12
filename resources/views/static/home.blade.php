@extends('layouts.public')

@push('styles')
<style>
    .hero-section {
        background-image: url('/images/hero.jpg');
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
    
    /* Mobile optimization for hero */
    @media (max-width: 768px) {
        .hero-section {
            background-attachment: scroll;
            min-height: 80vh;
            background-position: center top;
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
    
    /* Responsive banner text */
    @media (max-width: 768px) {
        .yellow-banner {
            font-size: 0.9rem;
            padding: 0.8rem 1.5rem;
            letter-spacing: 0.5px;
        }
    }
    
    @media (max-width: 480px) {
        .yellow-banner {
            font-size: 0.8rem;
            padding: 0.7rem 1rem;
            letter-spacing: 0.3px;
        }
    }
    
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
    
    .body-text {
        font-family: 'Source Sans Pro', sans-serif;
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
    
    /* New Sections Styles */
    
    /* Workout Options Section */
    .workout-options {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
        overflow: hidden;
        margin: 4rem 0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    /* Desktop border-radius */
    @media (min-width: 769px) {
        .workout-options {
            border-radius: 80px 10px 80px 10px;
        }
    }
    
    @media (max-width: 768px) {
        .workout-options {
            grid-template-columns: 1fr;
            margin: 2rem 0;
            border-radius: 20px;
        }
    }
    
    .workout-option {
        position: relative;
        padding: 3rem 2rem;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        min-height: 300px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
    }

    .workout-option::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1;
    }
    
    .workout-option-content {
        position: relative;
        z-index: 2;
        text-align: left;
    }

    /* --- Blocco Sinistra: Workout Online --- */
    .workout-online {
        background-image: url('/images/workout-online2.jpg');
    }

    .workout-online::before {
        background-color: rgba(54, 88, 61, 0.6); /* Overlay verde */
    }

    .workout-online h3,
    .workout-online p {
        color: #f6e849; /* Testo giallo */
    }

    .workout-online .workout-btn {
        background-color: #f6e849;
        color: #36583d;
    }

    /* --- Blocco Destra: Workout in Studio --- */
    .workout-studio {
        background-image: url('/images/banner2.jpg');
    }

    .workout-studio::before {
        background-color: rgba(246, 232, 73, 0.6); /* Overlay giallo semitrasparente */
    }

    .workout-studio h3,
    .workout-studio p {
        color: #36583d; /* Testo verde */
    }
    
    .workout-studio .workout-btn {
        background-color: #36583d;
        color: #f6e849;
    }
    
    /* --- Stili Comuni --- */
    .workout-option h3 {
        font-family: 'Montserrat', sans-serif;
        font-weight: 800;
        font-size: 3.5rem;
        text-transform: uppercase;
        margin-bottom: 1rem;
        line-height: 1.1;
    }

    .workout-option p {
        font-family: 'Source Sans Pro', sans-serif;
        margin-bottom: 1.5rem;
        line-height: 1.5;
        font-size: 1.1rem;
    }

    .workout-option .workout-btn {
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        text-transform: uppercase;
        padding: 0.8rem 1.5rem;
        border: none;
        border-radius: 50px;
        text-decoration: none;
        transition: transform 0.3s ease, background-color 0.3s ease;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .workout-option h3 {
            font-size: 2rem;
        }
    }
    
    @media (max-width: 480px) {
        .workout-option h3 {
            font-size: 1.5rem;
        }
    }    background-color: #f4e030;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }
    
    /* Emy Section */
    .emy-section {
        background-color: #f8f9fa;
        padding: 4rem 0;
    }
    
    @media (max-width: 768px) {
        .emy-section {
            padding: 3rem 0;
        }
    }
    
    .emy-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
    }
    
    @media (max-width: 968px) {
        .emy-content {
            grid-template-columns: 1fr;
            gap: 2rem;
            text-align: center;
        }
    }
    
    .emy-image {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }
    
    .emy-image img {
        width: 100%;
        height: auto;
        display: block;
    }
    
    .emy-text h2 {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        color: #36583d;
        font-size: 2.2rem;
        line-height: 1.2;
        margin-bottom: 2rem;
        text-transform: uppercase;
    }
    
    @media (max-width: 768px) {
        .emy-text h2 {
            font-size: 1.8rem;
        }
    }
    
    .emy-text p {
        font-family: 'Source Sans Pro', sans-serif;
        font-size: 1.1rem;
        line-height: 1.6;
        color: #555;
        margin-bottom: 1.5rem;
    }
    
    .emy-text .highlight {
        color: #36583d;
        font-weight: 600;
    }
    
    .emy-btn {
        background-color: #36583d;
        color: white;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
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
        margin-top: 1rem;
    }
    
    .emy-btn:hover {
        background-color: #2a4530;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    }
    
    /* Video Section */
    .video-section {
        background-color: #36583d;
        padding: 4rem 0;
        text-align: center;
    }
    
    @media (max-width: 768px) {
        .video-section {
            padding: 3rem 0;
        }
    }
    
    .video-container {
        position: relative;
        max-width: 800px;
        margin: 0 auto;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    }
    
    .video-container video {
        width: 100%;
        height: auto;
        display: block;
    }
    
    .video-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        color: #f6e849;
        font-size: 2.5rem;
        text-transform: uppercase;
        margin-bottom: 2rem;
        letter-spacing: 1px;
    }
    
    @media (max-width: 768px) {
        .video-title {
            font-size: 2rem;
        }
    }
    
    @media (max-width: 480px) {
        .video-title {
            font-size: 1.5rem;
        }
    }
    
    /* Slider Section */
    .slider-section {
        background-color: white;
        padding: 4rem 0;
        text-align: center;
    }
    
    @media (max-width: 768px) {
        .slider-section {
            padding: 3rem 0;
        }
    }
    
    .slider-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        color: #36583d;
        font-size: 2.5rem;
        text-transform: uppercase;
        margin-bottom: 3rem;
        letter-spacing: 1px;
    }
    
    @media (max-width: 768px) {
        .slider-title {
            font-size: 2rem;
        }
    }
    
    @media (max-width: 480px) {
        .slider-title {
            font-size: 1.5rem;
        }
    }
    
    .image-slider {
        position: relative;
        overflow: hidden;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .slider-track {
        display: flex;
        transition: transform 0.5s ease;
        gap: 1rem;
    }
    
    .slider-slide {
        flex: 0 0 auto;
        width: 300px;
        height: 200px;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    
    @media (max-width: 768px) {
        .slider-slide {
            width: 250px;
            height: 167px;
        }
    }
    
    @media (max-width: 480px) {
        .slider-slide {
            width: 200px;
            height: 133px;
        }
    }
    
    .slider-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .slider-slide:hover img {
        transform: scale(1.05);
    }
    
    .slider-nav {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-top: 2rem;
    }
    
    .slider-btn {
        background-color: #36583d;
        color: white;
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    
    .slider-btn:hover {
        background-color: #2a4530;
        transform: scale(1.1);
    }
    
    .slider-btn:disabled {
        background-color: #ccc;
        cursor: not-allowed;
        transform: none;
    }
    
    /* Testimonials Section */
    .testimonials-section {
        background-color: #f8f9fa;
        padding: 4rem 0;
    }
    
    @media (max-width: 768px) {
        .testimonials-section {
            padding: 3rem 0;
        }
    }
    
    .testimonials-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        color: #36583d;
        font-size: 2.2rem;
        text-transform: uppercase;
        margin-bottom: 3rem;
        text-align: center;
        letter-spacing: 1px;
    }
    
    @media (max-width: 768px) {
        .testimonials-title {
            font-size: 1.8rem;
        }
    }
    
    .testimonials-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }
    
    .testimonial-card {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        text-align: center;
        position: relative;
    }
    
    .testimonial-quote {
        font-family: 'Source Sans Pro', sans-serif;
        font-size: 1.1rem;
        line-height: 1.6;
        color: #555;
        margin-bottom: 1.5rem;
        font-style: italic;
    }
    
    .testimonial-author {
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        color: #36583d;
        font-size: 1rem;
    }
    
    .testimonial-stars {
        color: #f6e849;
        font-size: 1.2rem;
        margin-bottom: 1rem;
    }
    
    .testimonials-btn {
        background-color: #36583d;
        color: white;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
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
        margin: 0 auto;
        display: block;
        width: fit-content;
    }
    
    .testimonials-btn:hover {
        background-color: #2a4530;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    }
    
    /* Newsletter Section */
    .newsletter-section {
        background-color: #36583d;
        padding: 3rem 0;
        text-align: center;
    }
    
    .newsletter-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        color: white;
        font-size: 2rem;
        text-transform: uppercase;
        margin-bottom: 2rem;
        letter-spacing: 1px;
    }
    
    @media (max-width: 768px) {
        .newsletter-title {
            font-size: 1.5rem;
        }
    }
    
    .newsletter-form {
        display: flex;
        justify-content: center;
        gap: 1rem;
        max-width: 500px;
        margin: 0 auto;
    }
    
    @media (max-width: 480px) {
        .newsletter-form {
            flex-direction: column;
            gap: 1rem;
        }
    }
    
    .newsletter-input {
        flex: 1;
        padding: 1rem;
        border: none;
        border-radius: 8px;
        font-family: 'Source Sans Pro', sans-serif;
        font-size: 1rem;
    }
    
    .newsletter-btn {
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
        font-size: 0.9rem;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }
    
    .newsletter-btn:hover {
        background-color: #f4e030;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<div class="hero-section">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="yellow-banner">
            NON PARLIAMO DI ALLENAMENTO, PARLIAMO DI TE.
        </div>
    </div>
</div>

<!-- Allenamento Adattato Section -->
<div class="bg-white section-spacing">
    <div class="container-responsive">
        <div class="text-center mb-8 md:mb-12 lg:mb-16">
            <h2 class="section-title text-2xl md:text-3xl mb-4">ALLENAMENTO ADATTATO</h2>
            <div class="w-16 md:w-24 h-1 bg-gray-300 mx-auto"></div>
        </div>
        
        <!-- Programs Grid -->
        <div class="programs-grid">
            @if($featuredCourses->count() > 0)
                @foreach($featuredCourses as $course)
                    <a href="{{ route('courses.show', $course) }}" class="program-card" style="background-image: url('{{ Storage::url($course->image_url) }}');">
                        <div class="program-overlay">
                            <div class="program-content">
                                <h3 class="program-title">{{ $course->name }}</h3>
                                <p class="program-subtitle">{{ $course->duration_days }} Giorni</p>
                                <p class="program-duration">Online</p>
                                <span class="program-btn">SCOPRI DI PIÙ →</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            @else
                <p class="col-span-4 text-center text-gray-500">Nessun corso in primo piano al momento.</p>
            @endif
        </div>
    </div>
</div>

<!-- Workout Options Section -->
<div class="bg-white section-spacing">
    <div class="container-responsive">
        <div class="workout-options">
            <!-- Workout Online -->
            <div class="workout-option workout-online">
                <div class="workout-option-content">
                    <h3>WORKOUT<br>ONLINE</h3>
                    <p>Acquista subito il tuo workout online</p>
                    <a href="{{ route('static.workout-online') }}" class="workout-option-btn">
                        INIZIA ORA
                    </a>
                </div>
            </div>
            
            <!-- Workout in Studio -->
            <div class="workout-option workout-studio">
                <div class="workout-option-content">
                    <h3>WORKOUT IN<br>STUDIO</h3>
                    <p>Allenamento personalizzato e adattato</p>
                    <a href="{{ route('static.contact') }}" class="workout-option-btn">
                        PRENOTA ORA
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Emy Section -->
<div class="emy-section">
    <div class="container-responsive">
        <div class="emy-content">
            <div class="emy-image">
                <img src="/images/Emy-Workout-2.jpg" alt="Emy Workout" loading="lazy">
            </div>
            <div class="emy-text">
                <h2>SE HAI DECISO DI PRENDERE IN MANO LA TUA SALUTE E IL TUO BENESSERE, SEI NEL POSTO GIUSTO</h2>
                <p>L'allenamento <span class="highlight">adattato</span> è un percorso <span class="highlight">personalizzato</span> di attività fisica che si adegua perfettamente alle caratteristiche e agli obiettivi di ogni persona. È ideale sia per chi ha condizioni particolari, come sindromi dolorose o disturbi neuromotori.</p>
                <p>I programmi sono <span class="highlight">strutturati individualmente</span>, modulando intensità e durata in base alle specifiche esigenze che tu su un <span class="highlight">principiante</span>, un atleta, o abbia <span class="highlight">necessità specifiche</span> legate alla salute, l'allenamento viene calibrato per massimizzare i tuoi risultati in totale sicurezza.</p>
                <a href="{{ route('static.about') }}" class="emy-btn">
                    SCOPRI DI PIÙ
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Video Section -->
<div class="video-section">
    <div class="video-overlay"></div>
    <video autoplay loop muted poster="/images/Emy-Workout-2.jpg">
        <source src="/videos/1vd.mp4" type="video/mp4">
        Il tuo browser non supporta il tag video.
    </video>

</div>

<!-- Slider Section -->
<div class="slider-section">
    <div class="container-responsive">
        <h2 class="slider-title">I MIEI ALLENAMENTI</h2>
        <div class="image-slider" x-data="imageSlider()">
            <div class="slider-track" x-ref="sliderTrack" :style="{ 'transform': `translateX(-${currentSlide * (slideWidth + 16)}px)`, 'transition': isTransitioning ? 'transform 0.5s ease' : 'none' }">
                <div class="slider-slide">
                    <img src="/images/allenamento-1.jpg" alt="Allenamento 1" loading="lazy">
                </div>
                <div class="slider-slide">
                    <img src="/images/allenamento-2.jpg" alt="Allenamento 2" loading="lazy">
                </div>
                <div class="slider-slide">
                    <img src="/images/allenamento-3.jpg" alt="Allenamento 3" loading="lazy">
                </div>
                <div class="slider-slide">
                    <img src="/images/allenamento-4.jpg" alt="Allenamento 4" loading="lazy">
                </div>
                <div class="slider-slide">
                    <img src="/images/allenamento-5.jpg" alt="Allenamento 5" loading="lazy">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Testimonials Section -->
<div class="testimonials-section">
    <div class="container-responsive">
        <h2 class="testimonials-title">COSA DICONO I MIEI CLIENTI</h2>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-quote">
                    "Frequento questo studio da 2 anni e non ho mai smesso di cambiare. Gli allenamenti sono personalizzati e efficaci, ma il vero punto forte è la personal trainer, Emy. Persona meravigliosa, energica e positiva."
                </p>
                <p class="testimonial-author">Arianna Corti</p>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-quote">
                    "Mi alleno da poco con Emy, ma ho trovato subito un'ottima sintonia! Brava nella preparazione e alla sua professionalità unisce sempre piccole particolarità super consigliate."
                </p>
                <p class="testimonial-author">Maria Luigia</p>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">★★★★★</div>
                <p class="testimonial-quote">
                    "Ho avuto la fortuna di lavorare con Emy, una super trainer straordinaria la sento ogni aspetto. Non solo è incredibilmente preparata e professionale, ma va che la rende davvero unica è la sua profonda sensibilità e empatia verso le persone."
                </p>
                <p class="testimonial-author">Giorgia Lopez</p>
            </div>
        </div>
        <a href="https://www.google.com/search?sa=X&sca_esv=e6356c902485f8e1&hl=it-IT&tbm=lcl&sxsrf=ADLYWIKQIGJYEntaoojFHiJVTSDIj7k3IA:1722783273793&q=EMY+project+Studio+Recensioni&rflfq=1&num=20&stick=H4sIAAAAAAAAAONgkxK2MDYzMDU3NjCxNLQwMLA0MTU03MDI-IpR1tU3UqGgKD8rNblEIbikNCUzXyEoNTk1rzgzPy9zESt-eQAksNY_WgAAAA&rldimm=8360573049180094511&ved=2ahUKEwipxrzoy9uHAxVN_rsIHX_1L_oQ9fQKegQIMRAF&biw=1707&bih=811&dpr=1.13#lkt=LocalPoiReviews" class="testimonials-btn" target="_blank">
            VEDI TUTTE LE RECENSIONI
        </a>
    </div>
</div>

<!-- Newsletter Section -->
<div class="newsletter-section" id="newsletter-section">
    <div class="container-responsive">
        <h2 class="newsletter-title">RESTIAMO IN CONTATTO</h2>
        
        @if (session('success'))
            <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; text-align: center;">
                {{ session('success') }}
            </div>
        @endif

        <form class="newsletter-form" action="{{ route('newsletter.store') }}" method="POST">
            @csrf
            <input type="email" name="email" class="newsletter-input" placeholder="Il Tuo indirizzo Email" required value="{{ old('email') }}">
            <button type="submit" class="newsletter-btn">ISCRIVITI</button>
        </form>
        @error('email')
            <p class="text-danger" style="color: #dc3545; text-align: center; margin-top: 0.5rem;">{{ $message }}</p>
        @enderror
    </div>
</div>

@endsection

@push('scripts')
<script>
    function imageSlider() {
        return {
            currentSlide: 1,
            slideWidth: 300,
            totalSlides: 0,
            isTransitioning: true,

            init() {
                this.$nextTick(() => {
                    const track = this.$refs.sliderTrack;
                    const slides = Array.from(track.children);
                    this.totalSlides = slides.length;

                    if (this.totalSlides > 0) {
                        // Clone the first slide and add it to the end
                        const firstSlideClone = slides[0].cloneNode(true);
                        track.appendChild(firstSlideClone);

                        // Clone the last slide and add it to the beginning
                        const lastSlideClone = slides[this.totalSlides - 1].cloneNode(true);
                        track.insertBefore(lastSlideClone, slides[0]);
                    }

                    this.updateSlideWidth();
                    window.addEventListener('resize', () => this.updateSlideWidth());

                    setInterval(() => {
                        this.nextSlide();
                    }, 3000);

                    track.addEventListener('transitionend', () => {
                        if (this.currentSlide >= this.totalSlides + 1) {
                            this.isTransitioning = false;
                            this.currentSlide = 1;
                        } else if (this.currentSlide <= 0) {
                            this.isTransitioning = false;
                            this.currentSlide = this.totalSlides;
                        }
                    });
                });
            },

            updateSlideWidth() {
                if (window.innerWidth <= 480) {
                    this.slideWidth = 200;
                } else if (window.innerWidth <= 768) {
                    this.slideWidth = 250;
                } else {
                    this.slideWidth = 300;
                }
            },

            nextSlide() {
                this.currentSlide++;
                this.isTransitioning = true;
            }
        }
    }
</script>
@endpush
