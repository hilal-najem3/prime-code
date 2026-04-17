<section class="relative overflow-hidden">

    <div class="hero-slider">

        @foreach($data['slides'] ?? [] as $slide)
        <div class="hero-slide relative">

            {{-- Background --}}
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $slide['image'] }}')">
            </div>

            {{-- Content --}}
            <div class="relative z-10 flex items-center justify-center h-screen text-white text-center">

                <div>
                    <h1 class="text-5xl font-bold">
                        {{ $slide['title'][$lang] ?? '' }}
                    </h1>

                    <p class="mt-4">
                        {{ $slide['subtitle'][$lang] ?? '' }}
                    </p>
                </div>

            </div>

        </div>
        @endforeach

    </div>

</section>