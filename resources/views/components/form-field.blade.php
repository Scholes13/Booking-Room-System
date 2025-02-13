@props([
    'type' => 'text',
    'name',
    'label',
    'icon',
    'value' => '',
    'required' => false,
    'placeholder' => '',
    'options' => [],
    'id' => '',
    'rows' => 1
])

<div>
    <label for="{{ $id ?: $name }}" class="block text-gray-300 font-medium text-sm">{{ $label }}</label>
    <div class="flex items-center bg-white/30 backdrop-blur-md rounded-md p-3 shadow-md border border-white/20">
        <i class="{{ $icon }} text-gray-400 mr-2"></i>
        
        @if($type === 'select')
            <select 
                id="{{ $id ?: $name }}" 
                name="{{ $name }}"
                class="w-full bg-transparent border-none outline-none text-gray-900"
                {{ $required ? 'required' : '' }}
            >
                @if($placeholder)
                    <option value="" class="text-gray-900">{{ $placeholder }}</option>
                @endif
                @foreach($options as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" {{ $value == $optionValue ? 'selected' : '' }} class="text-black">
                        {{ $optionLabel }}
                    </option>
                @endforeach
            </select>
        @elseif($type === 'textarea')
            <textarea 
                id="{{ $id ?: $name }}"
                name="{{ $name }}"
                rows="{{ $rows }}"
                class="w-full bg-transparent border-none outline-none text-gray-900 placeholder-gray-400"
                {{ $required ? 'required' : '' }}
            >{{ $value }}</textarea>
        @else
            <input 
                type="{{ $type }}"
                id="{{ $id ?: $name }}"
                name="{{ $name }}"
                value="{{ $value }}"
                class="w-full bg-transparent border-none outline-none text-gray-900 placeholder-gray-400"
                {{ $required ? 'required' : '' }}
                @if($placeholder) placeholder="{{ $placeholder }}" @endif
            >
        @endif
    </div>
</div>