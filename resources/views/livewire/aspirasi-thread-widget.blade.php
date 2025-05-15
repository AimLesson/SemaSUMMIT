<div class="space-y-6">
    @foreach ($aspirasis as $aspirasi)
        <div class="border p-6 rounded-lg bg-white dark:bg-gray-800 shadow">
            <div class="border rounded-lg p-4 mb-4 bg-gray-100 dark:bg-gray-700">
                {{-- Header Aspirasi --}}
                <div class="flex items-center gap-3 mb-2 pb-2">
                    <img src="{{ $aspirasi->is_anonymous
                        ? 'https://ui-avatars.com/api/?name=Anonim'
                        : ($aspirasi->user?->avatar_url
                            ? asset('storage/' . $aspirasi->user->avatar_url)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($aspirasi->user?->name)) }}"
                        class="w-8 h-8 rounded-full object-cover overflow-hidden">
                    <span class="font-semibold text-black dark:text-white">
                        {{ $aspirasi->is_anonymous ? 'Anonim' : $aspirasi->user?->name }}
                    </span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $aspirasi->created_at->diffForHumans() }}
                    </span>
                </div>

                {{-- Gambar Aspirasi --}}
                @if ($aspirasi->image)
                    <div class="flex justify-center">
                        <img src="{{ asset('storage/' . $aspirasi->image) }}" class="mt-2 rounded-lg max-w-sm object-cover overflow-hidden">
                    </div>
                @endif

                {{-- Konten Aspirasi --}}
                <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded mt-2">
                    <div class="prose max-w-none text-md text-justify text-black dark:text-white">
                        {!! str($aspirasi->content)->sanitizeHtml() !!}
                    </div>
                </div>
            </div>

            {{-- Tombol toggle komentar --}}
            <button wire:click="toggleComments({{ $aspirasi->id }})"
                class="text-sm text-black dark:text-white border p-2 rounded-lg bg-gray-100 dark:bg-gray-700">
                {{ $showComments[$aspirasi->id] ?? false ? 'Sembunyikan Komentar' : 'Tampilkan Komentar' }}
            </button>

            {{-- Jika komentar ditampilkan --}}
            @if ($showComments[$aspirasi->id] ?? false)
                {{-- Form Komentar --}}
                <div class="ml-2 mt-2 border rounded-lg p-4 bg-gray-100 dark:bg-gray-700 mb-2">
                    <form wire:submit.prevent="postComment({{ $aspirasi->id }})" class="space-y-2">
                        <textarea wire:model.defer="commentContent.{{ $aspirasi->id }}" rows="2"
                            class="w-full p-2 border rounded text-sm bg-white dark:bg-gray-800 text-black dark:text-white" 
                            placeholder="Tulis komentar..." maxlength="500"></textarea>

                        <div class="flex items-center gap-3">
                            <label class="inline-flex items-center gap-1 text-sm text-black dark:text-white">
                                <input type="checkbox" wire:model="commentAnon.{{ $aspirasi->id }}" class="form-checkbox">
                                Anonim
                            </label>

                            <div wire:loading wire:target="commentImage.{{ $aspirasi->id }}" class="text-xs text-gray-500 dark:text-gray-400">
                                Mengunggah gambar...
                            </div>

                            <button type="submit"
                                class="bg-gray-100 dark:bg-gray-800 text-black dark:text-white border text-sm px-4 py-1 rounded-lg">
                                Kirim
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Komentar --}}
                <div class="mt-4 space-y-4">
                    @php
                        $totalKomentar = $aspirasi->komentar->count();
                        $displayKomentar = $aspirasi->komentar->take(($commentPage[$aspirasi->id] ?? 1) * $perPage);
                    @endphp
                    <div class="border bg-gray-100 dark:bg-gray-700 p-2 rounded-lg mb-2">
                        @foreach ($displayKomentar as $komentar)
                            <div class="ml-4 p-2 bg-gray-200 dark:bg-gray-800 border rounded-lg mb-2">
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $komentar->is_anonymous
                                            ? 'https://ui-avatars.com/api/?name=Anonim'
                                            : ($komentar->user?->avatar_url
                                                ? asset('storage/' . $komentar->user->avatar_url)
                                                : 'https://ui-avatars.com/api/?name=' . urlencode($komentar->user?->name)) }}"
                                            class="w-6 h-6 rounded-full">
                                        <span class="text-sm font-medium text-black dark:text-white">
                                            {{ $komentar->is_anonymous ? 'Anonim' : $komentar->user?->name }}
                                        </span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $komentar->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>

                                <p class="text-sm text-gray-800 dark:text-white">{{ $komentar->content }}</p>
                                @if ($komentar->image)
                                    <img src="{{ asset('storage/' . $komentar->image) }}" class="mt-1 rounded max-w-xs">
                                @endif

                                {{-- Toggle balasan --}}
                                <button wire:click="toggleReplies({{ $komentar->id }})"
                                    class="text-xs text-blue-500 dark:text-blue-300 hover:underline mt-2">
                                    {{ $showReplies[$komentar->id] ?? false ? 'Sembunyikan Balasan' : 'Tampilkan Balasan' }}
                                </button>

                                {{-- Balasan --}}
                                @if ($showReplies[$komentar->id] ?? false)
                                    {{-- Form Balasan --}}
                                    <div class="ml-4 mt-3">
                                        <form wire:submit.prevent="postReply({{ $komentar->id }})" class="space-y-2">
                                            <textarea wire:model.defer="replyContent.{{ $komentar->id }}" rows="2"
                                                class="w-full p-2 border rounded text-sm bg-white dark:bg-gray-800 text-black dark:text-white" 
                                                placeholder="Balas komentar..." maxlength="500"></textarea>

                                            <div class="flex items-center gap-3">
                                                <label class="inline-flex items-center gap-1 text-sm text-black dark:text-white">
                                                    <input type="checkbox" wire:model="replyAnon.{{ $komentar->id }}" class="form-checkbox">
                                                    Anonim
                                                </label>
                                                
                                                <div wire:loading wire:target="replyImage.{{ $komentar->id }}" class="text-xs text-gray-500 dark:text-gray-400">
                                                    Mengunggah gambar...
                                                </div>

                                                <button type="submit"
                                                    class="bg-gray-100 dark:bg-gray-800 border text-black dark:text-white text-sm px-3 py-1 rounded-lg">
                                                    Kirim
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    {{-- Daftar Balasan --}}
                                    @php
                                        $totalBalasan = $komentar->balasan->count();
                                        $displayBalasan = $komentar->balasan->take(($replyPage[$komentar->id] ?? 1) * $perPage);
                                    @endphp

                                    <div class="ml-4 mt-3 space-y-2">
                                        @foreach ($displayBalasan as $balasan)
                                            <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                                <div class="flex items-center justify-between mb-1">
                                                    <div class="flex items-center gap-2">
                                                        <img src="{{ $balasan->is_anonymous
                                                            ? 'https://ui-avatars.com/api/?name=Anonim'
                                                            : ($balasan->user?->avatar_url
                                                                ? asset('storage/' . $balasan->user->avatar_url)
                                                                : 'https://ui-avatars.com/api/?name=' . urlencode($balasan->user?->name)) }}"
                                                            class="w-5 h-5 rounded-full">
                                                        <span class="text-xs font-medium text-black dark:text-white">
                                                            {{ $balasan->is_anonymous ? 'Anonim' : $balasan->user?->name }}
                                                        </span>
                                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $balasan->created_at->diffForHumans() }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <p class="text-sm text-gray-700 dark:text-white">{{ $balasan->content }}</p>
                                                @if ($balasan->image)
                                                    <img src="{{ asset('storage/' . $balasan->image) }}" class="mt-1 rounded max-w-xs">
                                                @endif
                                            </div>
                                        @endforeach

                                        @if ($displayBalasan->count() < $totalBalasan)
                                            <button wire:click="loadMoreReplies({{ $komentar->id }})"
                                                class="text-xs text-blue-500 dark:text-blue-300 hover:underline">Tampilkan lebih banyak balasan</button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        @if ($displayKomentar->count() < $totalKomentar)
                            <button wire:click="loadMoreComments({{ $aspirasi->id }})"
                                class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Tampilkan lebih banyak komentar</button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @endforeach
</div>