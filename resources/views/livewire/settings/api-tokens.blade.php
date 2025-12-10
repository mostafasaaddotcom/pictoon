<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('API Tokens')" :subheading="__('Generate API tokens to access the API')">
        <div class="my-6 w-full space-y-6">
            @if ($plainTextToken)
                <flux:callout variant="warning">
                    <flux:callout.heading>{{ __('Token Created') }}</flux:callout.heading>
                    <flux:callout.text>
                        {{ __('Copy your token now. It won\'t be shown again.') }}
                    </flux:callout.text>
                </flux:callout>

                <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                    <code class="break-all text-sm">{{ $plainTextToken }}</code>
                </div>
            @endif

            <div class="space-y-4">
                @if ($hasToken)
                    <flux:text>{{ __('You have an active API token.') }}</flux:text>

                    <div class="flex gap-3">
                        <flux:button wire:click="createToken" wire:confirm="{{ __('This will revoke your existing token. Continue?') }}">
                            {{ __('Regenerate Token') }}
                        </flux:button>

                        <flux:button variant="danger" wire:click="revokeToken" wire:confirm="{{ __('Are you sure you want to revoke your token?') }}">
                            {{ __('Revoke Token') }}
                        </flux:button>
                    </div>
                @else
                    <flux:text>{{ __('You don\'t have an API token yet.') }}</flux:text>

                    <flux:button variant="primary" wire:click="createToken">
                        {{ __('Generate Token') }}
                    </flux:button>
                @endif
            </div>
        </div>
    </x-settings.layout>
</section>
