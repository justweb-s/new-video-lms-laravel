@extends('layouts.public')

@push('styles')
<style>
    .chi-sono-hero {
        background-image: url('/images/hero-chi-sono.jpg');
        background-size: cover;
        background-position: center top;
        background-repeat: no-repeat;
        background-attachment: scroll;
        min-height: 100vh;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    
    /* Mobile optimization for hero */
    @media (max-width: 768px) {
        .chi-sono-hero {
            background-attachment: scroll;
            min-height: 50vh;
        }
        .chi-sono-hero h1 {
            font-size: 1.2rem !important;
        }
        .chi-sono-hero .container-responsive {
            padding: 1.5rem;
        }
    }


    
    .chi-sono-hero .container-responsive {
        position: relative;
        z-index: 2;
        padding: 4rem;
        text-align: center;
    }

    .chi-sono-hero h1 {
        font-family: 'Montserrat', sans-serif;
        font-size: 3.5rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 20px;
        position: relative;
        z-index: 2;
        text-shadow: 2px 2px 8px rgba(0,0,0,0.6);
    }
    
    .chi-sono-hero .highlight {
        background: #36583d;
        color: #f6e849;
        padding: 5px 15px;
        border-radius: 25px;
        display: inline-block;
    }
    
    .intro-section {
        padding: 80px 0;
        background: #fff;
    }
    
    .intro-content {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 60px;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .intro-image {
        position: relative;
    }
    
    .intro-image img {
        width: 100%;
        height: auto;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(54, 88, 61, 0.2);
    }
    
    .intro-text h3 {
        font-family: 'Montserrat', sans-serif;
        font-size: 2.5rem;
        color: #36583d;
        margin-bottom: 20px;
        position: relative;
    }
    
    .intro-text h3::after {
        content: '';
        display: block;
        width: 60px;
        height: 4px;
        background: #f6e849;
        margin-top: 15px;
    }
    
    .intro-text p {
        font-family: 'Source Sans Pro', sans-serif;
        font-size: 1.1rem;
        line-height: 1.8;
        color: #333;
        margin-bottom: 20px;
    }
    
    .intro-text strong {
        color: #36583d;
        font-weight: 600;
    }
    
    .contact-email {
        display: inline-block;
        background: #36583d;
        color: #f6e849;
        padding: 12px 25px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        margin-top: 20px;
    }
    
    .contact-email:hover {
        background: #2a4530;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(54, 88, 61, 0.3);
    }
    
    .filosofia-section {
        background: #36583d;
        color: #f6e849;
        padding: 80px 0;
        position: relative;
        overflow: hidden;
    }
    
    .filosofia-section::before,
    .filosofia-section::after {
        content: '';
        position: absolute;
        background: rgba(246, 232, 73, 0.1);
        border-radius: 50%;
    }
    
    .filosofia-section::before {
        top: -50px;
        left: -50px;
        width: 200px;
        height: 200px;
    }
    
    .filosofia-section::after {
        bottom: -100px;
        right: -100px;
        width: 300px;
        height: 300px;
    }
    
    .filosofia-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
        position: relative;
        z-index: 1;
    }
    
    .filosofia-text h2 {
        font-family: 'Montserrat', sans-serif;
        font-size: 3.5rem;
        color: #fff;
        margin-bottom: 30px;
        line-height: 1.2;
        position: relative;
    }
    
    .filosofia-text h2::after {
        content: '';
        display: block;
        width: 80px;
        height: 4px;
        background: #f6e849;
        margin-top: 20px;
    }
    
    .filosofia-text p {
        font-family: 'Source Sans Pro', sans-serif;
        font-size: 1.2rem;
        line-height: 1.8;
        margin-bottom: 30px;
    }
    
    .filosofia-list {
        list-style: none;
        padding: 0;
        margin-bottom: 40px;
    }
    
    .filosofia-list li {
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        font-size: 1.2rem;
        font-family: 'Source Sans Pro', sans-serif;
    }
    
    .filosofia-list li::before {
        content: "⦿";
        color: #f6e849;
        margin-right: 15px;
        font-size: 1.5rem;
    }
    
    .filosofia-image {
        position: relative;
    }
    
    .filosofia-image-frame {
        border: 8px solid #f6e849;
        border-radius: 20px;
        overflow: hidden;
    }
    
    .filosofia-image img {
        display: block;
        width: 100%;
        height: auto;
    }
    
    .missione-section {
        padding: 80px 0;
        background: #f8f9fa;
    }
    
    .missione-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 60px;
        align-items: center;
    }
    
    .missione-image {
        position: relative;
    }
    
    .missione-image img {
        width: 100%;
        height: auto;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(54, 88, 61, 0.2);
    }
    
    .missione-text h2 {
        font-family: 'Montserrat', sans-serif;
        font-size: 2.5rem;
        color: #36583d;
        margin-bottom: 20px;
        position: relative;
    }
    
    .missione-text h2::after {
        content: '';
        display: block;
        width: 60px;
        height: 4px;
        background: #f6e849;
        margin-top: 15px;
    }
    
    .missione-text p {
        font-family: 'Source Sans Pro', sans-serif;
        font-size: 1.1rem;
        line-height: 1.8;
        color: #333;
        margin-bottom: 20px;
    }
    
    .testimonials-section {
        background: linear-gradient(135deg, #f6e849 0%, #f4e030 100%);
        padding: 80px 0;
    }
    
    .testimonials-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        text-align: center;
    }
    
    .testimonials-title {
        font-family: 'Montserrat', sans-serif;
        font-size: 2.5rem;
        color: #36583d;
        margin-bottom: 20px;
    }
    
    .testimonials-title::after {
        content: '';
        display: block;
        width: 60px;
        height: 4px;
        background: #36583d;
        margin: 20px auto;
    }
    
    .stars {
        margin-bottom: 40px;
    }
    
    .stars span {
        color: #36583d;
        font-size: 2rem;
        margin: 0 2px;
    }
    
    .testimonials-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 30px;
        margin-top: 40px;
    }
    
    .testimonial-card {
        background: rgba(255, 255, 255, 0.9);
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(54, 88, 61, 0.1);
        position: relative;
    }
    
    .testimonial-card::before {
        content: '"';
        font-size: 4rem;
        color: #36583d;
        position: absolute;
        top: -10px;
        left: 20px;
        font-family: serif;
    }
    
    .testimonial-text {
        font-family: 'Source Sans Pro', sans-serif;
        font-size: 1rem;
        line-height: 1.6;
        color: #333;
        margin-bottom: 20px;
        font-style: italic;
    }
    
    .testimonial-author {
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        color: #36583d;
        font-size: 1.1rem;
    }
    
    .newsletter-section {
        background: #36583d;
        padding: 60px 0;
    }
    
    .newsletter-content {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 20px;
        text-align: center;
    }
    
    .newsletter-title {
        font-family: 'Montserrat', sans-serif;
        font-size: 2rem;
        color: #f6e849;
        margin-bottom: 20px;
    }
    
    .newsletter-title::after {
        content: '';
        display: block;
        width: 60px;
        height: 4px;
        background: #f6e849;
        margin: 20px auto;
    }
    
    .newsletter-form {
        display: flex;
        gap: 15px;
        max-width: 500px;
        margin: 0 auto;
        flex-wrap: wrap;
    }
    
    .newsletter-form input {
        flex: 1;
        min-width: 250px;
        padding: 15px 20px;
        border: none;
        border-radius: 25px;
        font-family: 'Source Sans Pro', sans-serif;
        font-size: 1rem;
    }
    
    .newsletter-form button {
        background: #f6e849;
        color: #36583d;
        border: none;
        padding: 15px 30px;
        border-radius: 25px;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .newsletter-form button:hover {
        background: #f4e030;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(246, 232, 73, 0.3);
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }
    
    @media (max-width: 768px) {
        .chi-sono-hero h1 {
            font-size: 2.5rem;
        }
        
        .intro-content,
        .filosofia-content,
        .missione-content {
            grid-template-columns: 1fr;
            gap: 40px;
        }
        
        .filosofia-text {
            order: 2;
        }
        
        .filosofia-image {
            order: 1;
        }
        
        .filosofia-text h2 {
            font-size: 2.5rem;
        }
        
        .testimonials-grid {
            grid-template-columns: 1fr;
        }
        
        .newsletter-form {
            flex-direction: column;
        }
        
        .newsletter-form input {
            min-width: auto;
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<div class="chi-sono-hero">
    <div class="container-responsive">
        <h1><span class="highlight">Ciao, sono Emy</span></h1>
    </div>
</div>

<!-- Intro Section -->
<div class="intro-section">
    <div class="intro-content">
        <div class="intro-image">
            <img src="/images/chi-sono.jpg" alt="Emy - Personal Trainer" loading="lazy">
        </div>
        <div class="intro-text">
            <h3>La mia passione è aiutarti a vivere meglio</h3>
            <p>Il mio nome è <strong>Emy</strong>.</p>
            <p>Sono laureata in Scienze delle Attività Motorie con una specializzazione magistrale in Scienze delle Attività Motorie Preventive e Adattate. Nel corso degli anni, la mia sete di conoscenza e il desiderio di migliorarmi continuamente mi hanno portata a seguire una formazione sempre più mirata e specifica. Ho completato il mio percorso accademico con un Master in Preparazione Fisica Controllata, che ha arricchito e rafforzato le mie competenze nel settore.</p>
            <p>Grazie a questo solido bagaglio di conoscenze, oggi il mio lavoro si concentra su interventi individuali e di gruppo, con programmi di attività fisica adattata per diverse fasce di età e per persone con disabilità. Il mio obiettivo è promuovere la salute e il benessere psicofisico attraverso l'esercizio fisico, con programmi personalizzati che aiutino a raggiungere, mantenere o recuperare una condizione ottimale di benessere.</p>
            <a href="mailto:info@emyworkout.it" class="contact-email">info@emyworkout.it</a>
        </div>
    </div>
</div>

<!-- Filosofia Section -->
<div class="filosofia-section">
    <div class="filosofia-content">
        <div class="filosofia-text">
            <h2>La mia filosofia</h2>
            <p>La passione per questo lavoro nasce dalla mia esperienza personale: Ho scoperto quanto sia importante creare una routine quotidiana che includa:</p>
            <ul class="filosofia-list">
                <li>Esercizio Fisico</li>
                <li>Corretta Alimentazione</li>
                <li>Adeguato Riposo</li>
            </ul>
            <p>Questi tre fondamenti hanno trasformato la mia vita e oggi sono alla base del mio approccio professionale.</p>
        </div>
        <div class="filosofia-image">
            <div class="filosofia-image-frame">
                <img src="/images/workout-in-studio-filosofia.jpg" alt="Coach di fitness in azione" loading="lazy">
            </div>
        </div>
    </div>
</div>

<!-- Missione Section -->
<div class="missione-section">
    <div class="missione-content">
        <div class="missione-image">
            <img src="/images/la-mia-missione.jpg" alt="La mia missione" loading="lazy">
        </div>
        <div class="missione-text">
            <h2>La mia missione</h2>
            <p>Se hai deciso di prendere in mano la tua salute e il tuo benessere, sei nel posto giusto. Da anni mi impegno a trasmettere un messaggio chiaro: <strong>"La chiave per una vita equilibrata è DIVENTARE PROTAGONISTI DEL PROPRIO BENESSERE"</strong> seguendo l'onda dei tre fondamenti.</p>
            <p>Può essere difficile trovare tempo e motivazione per prendersi cura di sé, soprattutto quando ci si sente persi a causa di tentativi falliti con soluzioni fai da te. Se ti sei sentita/o così, è probabile che tu abbia bisogno di una guida esperta.</p>
            <p>La mia missione è guidarti attraverso un percorso personalizzato e adattato, pensato su misura per te, che ti aiuti a raggiungere i tuoi obiettivi rispettando il tuo stile di vita e le tue esigenze.</p>
            <p>Insieme, possiamo costruire un piano che rispetti il tuo stile di vita e che ti porti verso il successo desiderato.</p>
            <p><strong>La tua trasformazione inizia dal primo passo. Se sei pronta/o a prendere il controllo della tua salute e vivere al massimo? Io sono al tuo fianco.</strong></p>
        </div>
    </div>
</div>

<!-- Testimonials Section -->
<div class="testimonials-section">
    <div class="testimonials-content">
        <h2 class="testimonials-title">Cosa Dicono i Miei Clienti</h2>
        <div class="stars">
            <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
        </div>
        
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <p class="testimonial-text">Frequento questo studio da 2 anni e non ho intenzione di cambiare. Gli allenamenti sono personalizzati ed efficaci, ma il vero punto forte è la personal trainer, Emy. Persona meravigliosa, energica e positiva.</p>
                <p class="testimonial-author">Arianna Corti</p>
            </div>
            
            <div class="testimonial-card">
                <p class="testimonial-text">Mi alleno da poco con Emy, ma ho trovato subito moltissimi benefici alle mie problematiche grazie ai suoi preziosi consigli, alla sua preparazione e alla sua attenzione ad ogni piccolo particolare, super consigliata!</p>
                <p class="testimonial-author">Marzia Angius</p>
            </div>
            
            <div class="testimonial-card">
                <p class="testimonial-text">Una personal trainer con i fiocchi! Lavori tanto ma con il sorriso! Seria e preparata! E, cosa fondamentale, una bella persona!</p>
                <p class="testimonial-author">Silvia</p>
            </div>
        </div>
    </div>
</div>

<!-- Newsletter Section -->
<div class="newsletter-section" id="newsletter-section">
    <div class="newsletter-content">
        <h3 class="newsletter-title">Rimaniamo in contatto</h3>
        
        @if (session('success'))
            <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; text-align: center;">
                {{ session('success') }}
            </div>
        @endif

        <form class="newsletter-form" action="{{ route('newsletter.store') }}" method="POST">
            @csrf
            <input type="email" name="email" placeholder="Il Tuo Indirizzo Email" required value="{{ old('email') }}">
            <button type="submit">Iscriviti</button>
        </form>
        @error('email')
            <p class="text-danger" style="color: #dc3545; text-align: center; margin-top: 0.5rem;">{{ $message }}</p>
        @enderror
    </div>
</div>
@endsection
