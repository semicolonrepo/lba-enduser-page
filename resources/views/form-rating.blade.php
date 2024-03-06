@if ($formRating->type === 'text')
  <div class="mb-3">
    <label class="form-label" for="{{ $formRating->name }}">{{ $formRating->label }}</label>
    <input type="{{ $formRating->type }}" name="{{ $formRating->name }}" id="{{ $formRating->name }}" 
      class="{{ $formRating->className }} @if ($errors->has($formRating->name)) is-invalid @endif" value="{{ old($formRating->name) }}">
    <span class="invalid-feedback">{{ $errors->first($formRating->name) }}</span>
  </div>
@endif

@if ($formRating->type === 'textarea')
  <div class="mb-3">
    <label class="form-label" for="{{ $formRating->name }}">{{ $formRating->label }}</label>
    <textarea id="{{ $formRating->name }}" name="{{ $formRating->name }}" rows="3"
      class="{{ $formRating->className }} @if ($errors->has($formRating->name)) is-invalid @endif">{{ old($formRating->name) }}</textarea>
    <span class="invalid-feedback">{{ $errors->first($formRating->name) }}</span>
  </div>
@endif

@if ($formRating->type === 'select')
  <div class="mb-3">
    <label class="form-label" for="{{ $formRating->name }}">{{ $formRating->label }}</label>
    <select name="{{ $formRating->name }}" id="{{ $formRating->name }}" class="{{ $formRating->className }} @if ($errors->has($formRating->name)) is-invalid @endif">
      <option value="" disabled hidden selected></option>
      @foreach ($formRating->values as $option)
        <option value="{{ $option->value }}" {{ $option->selected ? 'selected' : '' }}>{{ $option->label }}</option>
      @endforeach
    </select>
    <span class="invalid-feedback">{{ $errors->first($formRating->name) }}</span>
  </div>
@endif

@if ($formRating->type === 'checkbox-group')
  <div class="mb-3">
    <label>{{ $formRating->label }}</label>
    <div class="checkbox-group @if ($errors->has($formRating->name)) is-invalid @endif">
      @foreach ($formRating->values as $option)
        <div class="formbuilder-checkbox">
          <input name="{{ $formRating->name }}[]" id="{{ $option->value }}" value="{{ $option->value }}" type="checkbox" 
            {{ ($option->selected || (is_array(old($formRating->name)) && in_array($option->value, old($formRating->name)))) ? 'checked' : '' }}>
          <label for="{{ $option->value }}">{{ $option->label }}</label>
        </div>
      @endforeach
    </div>
    <span class="invalid-feedback">{{ $errors->first($formRating->name) }}</span>
  </div>
@endif

@if ($formRating->type === 'starRating')
  <div class="mb-3">
    <label>{{ $formRating->label }}</label>
    <div id="{{ $formRating->name }}"></div>
    <input type="hidden" name="{{ $formRating->name }}" class="@if ($errors->has($formRating->name)) is-invalid @endif">
    <span class="invalid-feedback">{{ $errors->first($formRating->name) }}</span>
  </div>
@endif
