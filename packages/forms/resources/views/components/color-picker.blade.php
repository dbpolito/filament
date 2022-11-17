<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <x-filament::input.affixes
        :state-path="$statePath"
        :prefix="$getPrefixLabel()"
        :prefix-action="$getPrefixAction()"
        :prefix-icon="$getPrefixIcon()"
        :suffix="$getSuffixLabel()"
        :suffix-action="$getSuffixAction()"
        :suffix-icon="$getSuffixIcon()"
        class="filament-forms-text-input-component"
        :attributes="$getExtraAttributeBag()"
    >
        <div {{ $attributes->merge($getExtraAttributes(), escape: true)->class(['filament-forms-color-picker-component flex items-center space-x-1 rtl:space-x-reverse group']) }}>
            <div
                x-ignore
                ax-load
                ax-load-src="/js/filament/forms/components/color-picker.js?v={{ \Composer\InstalledVersions::getVersion('filament/support') }}"
                x-data="colorPickerFormComponent({
                    isAutofocused: @js($isAutofocused()),
                    isDisabled: @js($isDisabled()),
                    state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }}
                })"
                x-on:keydown.esc="isOpen() && $event.stopPropagation()"
                {{ $getExtraAlpineAttributeBag()->class(['relative flex-1']) }}
            >
                <input
                    x-ref="input"
                    type="text"
                    dusk="filament.forms.{{ $getStatePath() }}"
                    id="{{ $getId() }}"
                    x-model="state"
                    x-on:click="togglePanelVisibility()"
                    x-on:keydown.enter.stop.prevent="togglePanelVisibility()"
                    autocomplete="off"
                    {!! $isDisabled() ? 'disabled' : null !!}
                    {!! ($placeholder = $getPlaceholder()) ? "placeholder=\"{$placeholder}\"" : null !!}
                    @if (! $isConcealed())
                        {!! $isRequired() ? 'required' : null !!}
                    @endif
                    {{ $getExtraInputAttributeBag()->class([
                        'text-gray-900 block w-full transition duration-75 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500',
                        'border-gray-300 dark:border-gray-600' => ! $errors->has($getStatePath()),
                        'border-danger-600 ring-danger-600 dark:border-danger-400 dark:ring-danger-400' => $errors->has($getStatePath()),
                        'rounded-l-lg' => ! ($getPrefixLabel() || $getPrefixIcon()),
                        'rounded-r-lg' => ! ($getSuffixLabel() || $getSuffixIcon()),
                    ]) }}
                />

                <span
                    x-cloak
                    class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none rtl:right-auto rtl:left-0 rtl:pl-2"
                >
                    <span
                        x-bind:style="{ 'background-color': state }"
                        class="filament-forms-color-picker-component-preview relative overflow-hidden rounded-md w-7 h-7"
                    ></span>
                </span>

                <div
                    x-cloak
                    x-ref="panel"
                    x-float.placement.bottom-start.offset.flip.shift="{ offset: 8 }"
                    wire:ignore.self
                    wire:key="{{ $this->id }}.{{ $getStatePath() }}.{{ $field::class }}.panel"
                    @class([
                        'hidden absolute z-10 shadow-lg',
                        'opacity-70 pointer-events-none' => $isDisabled(),
                    ])
                >
                    @php
                        $tag = match ($getFormat()) {
                            'hsl' => 'hsl-string',
                            'rgb' => 'rgb-string',
                            'rgba' => 'rgba-string',
                            default => 'hex',
                        } . '-color-picker';
                    @endphp

                    <{{ $tag }} color="{{ $getState() }}" />
                </div>
            </div>

        </div>
    </x-filament::input.affixes>
</x-dynamic-component>
