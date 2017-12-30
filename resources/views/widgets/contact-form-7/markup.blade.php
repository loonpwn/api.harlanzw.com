<section class="section contact-form">
  <div class="container ">
    <h2>
      {!! $title !!}
    </h2>
    <p>{!! $sub_title !!}</p>
    {!! do_shortcode('[contact-form-7 id="' . $contact_form_id . '" title=""]') !!}
  </div>
</section>