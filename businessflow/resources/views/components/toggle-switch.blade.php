{{--
    Plain inline-styled switch (no Tailwind classes) so it renders
    correctly even if the compiled CSS bundle on a shared-hosting deploy
    hasn't been rebuilt/re-uploaded yet — only this file and Alpine (both
    already shipped) are needed. Every rule for a given element lives in
    one single :style binding — mixing a static style="" with a :style=""
    on the same element makes Alpine overwrite the static one entirely.
--}}
@props(['name', 'checked' => false])

<label style="display:inline-flex;align-items:center;cursor:pointer;" x-data="{ on: {{ $checked ? 'true' : 'false' }} }">
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" name="{{ $name }}" value="1" x-model="on"
        style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;">
    <span
        :style="'display:inline-block;width:40px;height:24px;border-radius:9999px;position:relative;transition:background-color .15s ease;flex-shrink:0;background-color:' + (on ? '#ea580c' : '#94a3b8')">
        <span :style="'position:absolute;top:2px;left:2px;width:20px;height:20px;background:#fff;border-radius:9999px;transition:transform .15s ease;transform:translateX(' + (on ? 16 : 0) + 'px)'"></span>
    </span>
</label>
