@php
    $appName = config('app.name', 'Laravel');

    // Start with values from the View Composer (for static pages)
    $pageTitle = $__env->yieldContent('title');
    if (empty($pageTitle) && isset($course) && $course->meta_title) {
        $pageTitle = $course->meta_title;
    } elseif (empty($pageTitle)) {
        $pageTitle = $defaultSeoTitle ?? '';
    }

    $pageDescription = $__env->yieldContent('meta_description');
    if (empty($pageDescription) && isset($course) && $course->meta_description) {
        $pageDescription = $course->meta_description;
    } elseif (empty($pageDescription)) {
        $pageDescription = $defaultSeoDescription ?? '';
    }

    // Fallbacks
    if (empty($pageTitle) && isset($course)) $pageTitle = $course->name;
    if (empty($pageDescription) && isset($course)) $pageDescription = Str::limit(strip_tags($course->description), 155);
    if (empty($pageDescription)) $pageDescription = $appName . ' - Piattaforma corsi e allenamenti online e in studio.';

    $fullTitle = $pageTitle ? ($pageTitle . ' | ' . $appName) : $appName;
    $robots = $__env->yieldContent('robots', 'index,follow');
    $canonical = $__env->yieldContent('canonical', url()->current());
    $image = $__env->yieldContent('meta_image', asset('images/favicon-studio.png'));
@endphp

<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $description }}">
<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonical }}" />

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:title" content="{{ $title ?: $appName }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $canonical }}">
<meta name="twitter:title" content="{{ $title ?: $appName }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">
