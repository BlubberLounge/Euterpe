<div class="flex justify-center">
    <div class="bg-white p-4 rounded-2xl inline-block">
        {!! QrCode::size(180)->generate($jam->getJoinUrl()) !!}
    </div>
</div>
