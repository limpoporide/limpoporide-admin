@extends('website.common.main')
@section('content')

    <!-- #header -->
    <style>
    /* Paragraph styling */
    
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #fff;
        color: #000;
        overflow-x: hidden;
    }
    
    p {
        margin-top: 1rem;
        margin-bottom: 1rem;
        line-height: 1.6;
    }
    
    /* Heading styles */
    h1 {
        margin-top: 2rem;
        margin-bottom: 1rem;
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1.2;
    }
    
    h2 {
        margin-top: 1.75rem;
        margin-bottom: 0.875rem;
        font-size: 2rem;
        font-weight: 700;
        line-height: 1.3;
    }
    
    h3 {
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        font-size: 1.75rem;
        font-weight: 600;
        line-height: 1.3;
    }
    
    h4 {
        margin-top: 1.25rem;
        margin-bottom: 0.625rem;
        font-size: 1.5rem;
        font-weight: 600;
        line-height: 1.4;
    }
    
    h5 {
        margin-top: 1rem;
        margin-bottom: 0.5rem;
        font-size: 1.25rem;
        font-weight: 600;
        line-height: 1.4;
    }
    
    h6 {
        margin-top: 1rem;
        margin-bottom: 0.5rem;
        font-size: 1rem;
        font-weight: 600;
        line-height: 1.4;
    }
    
    /* Span styling */
    span {
        line-height: inherit;
    }
    
    /* Container styling */
    .container {
        width: 100%;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    /* Optional: First paragraph after heading - remove top margin */
    h1 + p,
    h2 + p,
    h3 + p,
    h4 + p,
    h5 + p,
    h6 + p {
        margin-top: 0.5rem;
    }
    
    /* Optional: Reset margin for first and last elements */
    .container > *:first-child {
        margin-top: 0;
    }
    
    .container > *:last-child {
        margin-bottom: 0;
    }
</style>

<section>
    <div class="container" style="margin-top:140px;margin-bottom:100px;">
        {!! $data->value !!}
    </div>
</section>

@endsection