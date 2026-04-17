{{--
|--------------------------------------------------------------------------
| Features Block - Grid
|--------------------------------------------------------------------------
--}}

<section class="py-16 
    {{ ($settings['background'] ?? '') === 'light' ? 'bg-gray-50' : '' }}">

    <div class="container mx-auto px-6">

        {{-- Title --}}
        @if(!empty($data['title']))
        <h2 class="text-3xl font-bold text-center mb-12">
            {{ $data['title'][$lang] ?? $data['title']['en'] ?? '' }}
        </h2>
        @endif

        {{-- Grid --}}
        <div class="grid gap-8
            {{ ($settings['columns'] ?? 3) == 2 ? 'md:grid-cols-2' : '' }}
            {{ ($settings['columns'] ?? 3) == 3 ? 'md:grid-cols-3' : '' }}
            {{ ($settings['columns'] ?? 3) == 4 ? 'md:grid-cols-4' : '' }}
        ">

            @foreach($data['items'] ?? [] as $item)

            <div class="p-6 bg-white rounded-xl shadow-sm text-center">

                {{-- Icon --}}
                @if(!empty($item['icon']))
                <div class="mb-4 text-3xl">
                    <i class="icon-{{ $item['icon'] }}"></i>
                </div>
                @endif

                {{-- Title --}}
                <h3 class="text-lg font-semibold">
                    {{ $item['title'][$lang] ?? '' }}
                </h3>

                {{-- Description --}}
                <p class="mt-2 text-gray-600">
                    {{ $item['description'][$lang] ?? '' }}
                </p>

            </div>

            @endforeach

        </div>

    </div>

</section>