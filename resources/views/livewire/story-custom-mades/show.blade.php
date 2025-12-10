<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <flux:button variant="ghost" :href="route('story-custom-mades.index', $story)" wire:navigate>
                <flux:icon name="arrow-left" class="h-4 w-4" />
            </flux:button>
            <div>
                <flux:heading size="xl">{{ __('Custom Story Details') }}</flux:heading>
                <flux:text class="text-zinc-500">{{ $story->idea }}</flux:text>
            </div>
        </div>
        <flux:button :href="route('story-custom-mades.edit', [$story, $customMade])" wire:navigate>
            {{ __('Edit') }}
        </flux:button>
    </div>

    @if (session('message'))
        <flux:callout variant="success">
            {{ session('message') }}
        </flux:callout>
    @endif

    @if (session('error'))
        <flux:callout variant="danger">
            {{ session('error') }}
        </flux:callout>
    @endif

    <div class="mx-auto w-full max-w-2xl space-y-6">
        <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">{{ $customMade->child_name }}</flux:heading>
                    <flux:badge size="lg" color="{{ match($customMade->status) {
                        'pending' => 'yellow',
                        'processing' => 'blue',
                        'completed' => 'green',
                        'failed' => 'red',
                        default => 'zinc'
                    } }}">
                        {{ ucfirst($customMade->status) }}
                    </flux:badge>
                </div>

                <flux:separator />

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Gender') }}
                        </flux:text>
                        <flux:text class="mt-1">{{ ucfirst($customMade->child_gender) }}</flux:text>
                    </div>

                    <div>
                        <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Age') }}
                        </flux:text>
                        <flux:text class="mt-1">{{ $customMade->child_age }} {{ __('years old') }}</flux:text>
                    </div>
                </div>

                @if ($customMade->child_image_url)
                    <flux:separator />

                    <div>
                        <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Child Image') }}
                        </flux:text>
                        <div class="mt-2">
                            <img src="{{ $customMade->child_image_url }}" alt="{{ $customMade->child_name }}" width="100" height="100" class="rounded-lg object-cover">
                        </div>
                    </div>
                @endif

                @if ($customMade->pdf_final_url)
                    <flux:separator />

                    <div>
                        <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Final PDF') }}
                        </flux:text>
                        <div class="mt-2">
                            <flux:button variant="primary" href="{{ $customMade->pdf_final_url }}" target="_blank">
                                <flux:icon name="document-arrow-down" class="mr-2 h-4 w-4" />
                                {{ __('Download PDF') }}
                            </flux:button>
                        </div>
                    </div>
                @endif

                <flux:separator />

                <div>
                    <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                        {{ __('Image Generation Progress') }}
                    </flux:text>
                    <div class="mt-2 flex items-center gap-4">
                        <flux:text>
                            {{ $this->completedImagesCount }} / {{ $this->requiredImagesCount }} {{ __('images completed') }}
                        </flux:text>
                        @if ($this->canRegeneratePdf)
                            <flux:button wire:click="regeneratePdf" wire:loading.attr="disabled" wire:target="regeneratePdf">
                                <flux:icon name="arrow-path" class="-ml-1 mr-2 h-4 w-4" wire:loading.class="animate-spin" wire:target="regeneratePdf" />
                                <span wire:loading.remove wire:target="regeneratePdf">{{ __('Regenerate PDF') }}</span>
                                <span wire:loading wire:target="regeneratePdf">{{ __('Sending...') }}</span>
                            </flux:button>
                        @endif
                    </div>
                </div>

                <flux:separator />

                <div class="grid grid-cols-2 gap-6 text-sm">
                    <div>
                        <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Created') }}
                        </flux:text>
                        <flux:text class="mt-1">{{ $customMade->created_at->format('M d, Y H:i') }}</flux:text>
                    </div>

                    <div>
                        <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Last Updated') }}
                        </flux:text>
                        <flux:text class="mt-1">{{ $customMade->updated_at->format('M d, Y H:i') }}</flux:text>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
