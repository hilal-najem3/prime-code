{{--
|--------------------------------------------------------------------------
| Hero Block - Video Variant
|--------------------------------------------------------------------------
|
| High-end hero section with:
| - Video background
| - Overlay support
| - Dynamic positioning
|
--}}

<section class="relative w-full overflow-hidden
    {{ ($settings['height'] ?? '') === 'full' ? 'h-screen' : 'py-20' }}">

    {{-- Background Video --}}
    @if(!empty($data['background_video']))
    <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover">
        <source src="{{ $data['background_video'] }}" type="video/mp4">
    </video>
    @elseif(!empty($data['background_image']))
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $data['background_image'] }}')">
    </div>
    @endif

    {{-- Overlay --}}
    @if(($settings['overlay'] ?? false))
    <div class="absolute inset-0
            {{ ($settings['overlay_color'] ?? 'dark') === 'dark'
                ? 'bg-black/60'
                : 'bg-white/40' }}">
    </div>
    @endif

    {{-- Content --}}
    <div class="relative z-10 flex items-center justify-center h-full text-center px-6">

        <div class="max-w-3xl">

            {{-- Title --}}
            @if(!empty($data['title']))
            <h1 class="text-4xl md:text-6xl font-bold text-white">
                {{ $data['title'][$lang] ?? $data['title']['en'] ?? '' }}
            </h1>
            @endif

            {{-- Subtitle --}}
            @if(!empty($data['subtitle']))
            <p class="mt-4 text-lg text-white/90">
                {{ $data['subtitle'][$lang] ?? $data['subtitle']['en'] ?? '' }}
            </p>
            @endif

            {{-- Button --}}
            @if(!empty($data['button']['text']))
            <a href="{{ $data['button']['url'] ?? '#' }}"
                class="inline-block mt-6 px-6 py-3 bg-white text-black font-medium">
                {{ $data['button']['text'][$lang] ?? '' }}
            </a>
            @endif

        </div>

    </div>

</section>