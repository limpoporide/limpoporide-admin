@extends('admin.includes.main')
@section('content')

<style>
    .card-body {
      margin-top: 20px;
    }
</style>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Privacy Policy</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Home</li>
          <li class="breadcrumb-item">CMS</li>
          <li class="breadcrumb-item active">Privacy Policy Driver</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

         <div class="card">
            <div class="card-body">
              
                <form class="form_submit" method="post" action="{{url('admin/terms_conditions')}}">
                    @csrf
                    <input type="hidden" name="key" value="{{ $data->key }}" />
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="edit_aboutus">Title<b class="text-danger">*</b></label>
                                <div class="Adddiscountinner">
                                    <input type="text" name="title" id="title" value="{{ $data->name }}" class="form-control" row=4>
                                    <span class="error error-title"></span>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="edit_aboutus">Description <b class="text-danger">*</b></label>
                                <div class="Adddiscountinner">
                                    <textarea id="edit_aboutus" name="description" rows="4" cols="53" class="ckeditor">{{ $data->value }}</textarea>
                                    <span class="error error-description"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="modal-footer">
                        <div class="updateBtnLoader">
                            <input type="submit" value="Update" class="btn btn-info btn-round">
                        </div>
                    </div>
                </form>

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->


@endsection


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script>
    $(document).ready(function() {
       $('.ckeditor').ckeditor();
    });
</script>

