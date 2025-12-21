@php
    use Illuminate\Support\Facades\Session;
    use Illuminate\Support\Facades\App;

    // Lấy theme từ session, mặc định là light
    $currentTheme = Session::get('theme', 'light');
    
    // Tailwind dark mode hoạt động dựa trên class 'dark'. 
    // Nếu session lưu là 'dark' thì thêm class 'dark', ngược lại để trống.
    $htmlClass = $currentTheme === 'dark' ? 'dark' : '';
    
    // Thêm theme class để kích hoạt CSS variables
    $themeClass = $currentTheme === 'dark' ? 'theme-dark' : 'theme-light';
    $htmlClass = $htmlClass . ' ' . $themeClass;
    
    $locale = App::currentLocale();
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" class="{{ $htmlClass }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'PoLuv Tasks') }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600;700&family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class', // Quan trọng: Dark mode kích hoạt bằng class 'dark' ở thẻ html
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                        script: ['Dancing Script', 'cursive'],
                    },
                    colors: {
                        header: '#F2EAEA',
                        main: '#FAF7F2',
                        card: '#F5E6D3',
                        completed: '#EED6B5',
                        pending: '#C8AFA9',
                    },
                    boxShadow: {
                        'smooth': '0 4px 20px rgba(0,0,0,0.03)',
                        'task': '0 2px 8px rgba(92, 67, 46, 0.08)',
                    },
                }
            }
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body style="background-color: var(--color-background); color: var(--color-on-background);" class="antialiased font-sans min-h-screen flex flex-col transition-colors duration-300 relative">
    
    @yield('content')

    @livewireScripts
    <script>window.theme = '{{ $currentTheme }}'</script>
    
    @stack('scripts')
    
    @yield('components')
</body>
</html>
