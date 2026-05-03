@props(['field' => 'website'])

{{-- Honeypot — humans never see this. Bots that submit every visible input
     get rejected server-side. See docs/PUBLIC_FORM_SECURITY.md. --}}
<div aria-hidden="true" style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden">
    <label>
        Website
        <input type="text" name="{{ $field }}" tabindex="-1" autocomplete="off" value="">
    </label>
</div>
