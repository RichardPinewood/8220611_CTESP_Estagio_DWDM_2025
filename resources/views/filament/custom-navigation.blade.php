@php
    $navigation = \Filament\Facades\Filament::getNavigation();
@endphp

<nav class="flex items-center space-x-4 mr-4">
    @foreach($navigation as $group)
        @if($group->getLabel())
            <!-- Group with dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                    {{ $group->getLabel() }}
                    <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
                    @foreach($group->getItems() as $item)
                        <a href="{{ $item->getUrl() }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            {{ $item->getLabel() }}
                        </a>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Individual items -->
            @foreach($group->getItems() as $item)
                <a href="{{ $item->getUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                    {{ $item->getLabel() }}
                </a>
            @endforeach
        @endif
    @endforeach
</nav>