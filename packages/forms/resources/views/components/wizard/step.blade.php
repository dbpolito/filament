<div
    x-ref="step-{{ $getId() }}"
    x-bind:class="{ 'invisible h-0 overflow-y-hidden': step !== @js($getId()) }"
    x-on:expand-concealing-component.window="
        error = $el.querySelector('[data-validation-error]')

        if (! error) {
            return
        }

        if (! isStepAccessible(step, @js($getId()))) {
            return
        }

        step = @js($getId())

        if (document.body.querySelector('[data-validation-error]') !== error) {
            return
        }

        setTimeout(() => $el.scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'start' }), 200)
    "
    {{
        $attributes
            ->merge([
                'aria-labelledby' => $getId(),
                'id' => $getId(),
                'role' => 'tabpanel',
                'tabindex' => '0',
            ], escape: true)
            ->merge($getExtraAttributes(), escape: true)
            ->class(['focus:outline-none filament-forms-wizard-component-step'])
    }}
>
    {{ $getChildComponentContainer() }}
</div>
