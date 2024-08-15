@php $message->getMessage(); @endphp
<article id="message">
    <div role="alert" class="alert alert-{!! $message->style !!} alert-dismissible text-monospace sticky-top text-center" style="margin-bottom: 0px;">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <strong class="text-black">Alert:  </strong><span class="link-info">{!! $message->message !!}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</article>