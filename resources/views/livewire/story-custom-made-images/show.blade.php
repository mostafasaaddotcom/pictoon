<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <flux:button variant="ghost" :href="route('custom-made-images.index', [$story, $customMade])" wire:navigate>
                <flux:icon name="arrow-left" class="h-4 w-4" />
            </flux:button>
            <div>
                <flux:heading size="xl">{{ __('Image Details') }}</flux:heading>
                <flux:text class="text-zinc-500">{{ $customMade->child_name }}</flux:text>
            </div>
        </div>
        <flux:button :href="route('custom-made-images.edit', [$story, $customMade, $image])" wire:navigate>
            {{ __('Edit') }}
        </flux:button>
    </div>

    <div class="mx-auto w-full max-w-2xl space-y-6">
        <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <flux:badge size="lg" color="{{ match($image->image_type) {
                            'page' => 'blue',
                            'cover_front' => 'green',
                            'cover_back' => 'purple',
                            default => 'zinc'
                        } }}">
                            {{ match($image->image_type) {
                                'page' => __('Page :num', ['num' => $image->page_number]),
                                'cover_front' => __('Front Cover'),
                                'cover_back' => __('Back Cover'),
                                default => $image->image_type
                            } }}
                        </flux:badge>
                        <flux:badge size="lg" color="{{ match($image->status) {
                            'pending' => 'yellow',
                            'processing' => 'blue',
                            'completed' => 'green',
                            'failed' => 'red',
                            default => 'zinc'
                        } }}">
                            {{ ucfirst($image->status) }}
                        </flux:badge>
                    </div>
                </div>

                @if ($image->image_url)
                    <flux:separator />

                    <div>
                        <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Image') }}
                        </flux:text>
                        <div class="mt-2">
                            <img src="{{ $image->image_url }}" alt="Page {{ $image->page_number }}" class="max-h-96 rounded-lg object-contain">
                        </div>
                    </div>
                @endif

                <flux:separator />

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Page Number') }}
                        </flux:text>
                        <flux:text class="mt-1">{{ $image->page_number }}</flux:text>
                    </div>

                    <div>
                        <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Image Type') }}
                        </flux:text>
                        <flux:text class="mt-1">{{ ucfirst(str_replace('_', ' ', $image->image_type)) }}</flux:text>
                    </div>

                    @if ($image->reference_number)
                        <div>
                            <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                {{ __('Reference Number') }}
                            </flux:text>
                            <flux:text class="mt-1">{{ $image->reference_number }}</flux:text>
                        </div>
                    @endif
                </div>

                <flux:separator />

                <div class="grid grid-cols-2 gap-6 text-sm">
                    <div>
                        <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Created') }}
                        </flux:text>
                        <flux:text class="mt-1">{{ $image->created_at->format('M d, Y H:i') }}</flux:text>
                    </div>

                    <div>
                        <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Last Updated') }}
                        </flux:text>
                        <flux:text class="mt-1">{{ $image->updated_at->format('M d, Y H:i') }}</flux:text>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
