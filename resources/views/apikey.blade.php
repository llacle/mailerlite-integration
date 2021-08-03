@extends('layouts.master')

@section('title', 'API Keys')

@section('content')
    <h2>API Key @empty($apikey_object)<a class="btn btn-primary mb-4 float-right" data-toggle="collapse" href="#collapseForm" role="button" aria-expanded="false" aria-controls="collapseForm">Add New API Key</a>@endempty($apikey_object)</h2>
    <div id="feedback"></div>

    @empty($apikey_object)
        <div class="collapse w-100 pb-2" id="collapseForm">
            <div class="card card-body w-100">
                <form id="apiForm" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label for="api_key">API Key</label>
                        <input type="text" class="form-control" name="api_key" id="api_key" required placeholder="Enter a MailerLite API Key">
                    </div>
                    <button type="submit" class="btn btn-primary float-right">Save</button>
                </form>
            </div>
        </div>

        <div class="jumbotron">
          <p class="text-center">Please add an API key to get started.</p>
        </div>
    @endempty($apikey_object)

    @isset($apikey_object)
    <div class="my-3 p-3 bg-white rounded shadow-sm">
        <div class="media text-muted pt-3">
            <div class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <strong class="text-gray-dark">{{ $apikey_object->apikey_value }}</strong>
                    <a href="#" class="delete_key">remove</a>
                </div>
            </div>
        </div>
    </div>
    @endisset
@stop

@section('footer_scripts')
    <script>
        $(document).ajaxError(function(e, xhr, settings, exception) {
            var msg = '';

            if('message' in xhr.responseJSON){
                msg = xhr.responseJSON.message;
            }else{
                msg = xhr.status+' '+xhr.statusText
            }

            $('#feedback').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+msg+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        });

        $(document).on('submit','#apiForm',function(event){
            event.preventDefault()

            $.ajax({
                url: '/apikey/add',
                type: 'POST',
                data: $('#apiForm').serialize(),
                beforeSend: function(){
                    $("#feedback").html("");
                    $("#apiForm button[type=submit]").attr('disabled', 'disabled')
                },
                complete: function() {
                    $("#apiForm button[type=submit]").removeAttr('disabled')
                },
                error: function(xhr, txtStatus, txtError){
                    if('message' in xhr.responseJSON){
                        $('#feedback').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+xhr.responseJSON.message+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    }
                },
                success: function(result) {
                    if('error' in result && result['error'] != false && 'message' in result) {
                        $('#feedback').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+result.message+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    }else{
                        window.location.replace('/apikey');
                    }
                }
            });

            return false;
        });

        $(document).on('click','.delete_key',function(event){
            event.preventDefault()

            $.ajax({
                url: '/apikey',
                type: 'DELETE',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                error: function(xhr, txtStatus, txtError){
                    if ('message' in xhr.responseJSON) {
                        $('#feedback').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+xhr.responseJSON.message+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    }
                },
                success: function(result) {
                    if('error' in result && result['error'] != false && 'message' in result) {
                        $('#feedback').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+result.message+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    }else{
                        window.location.replace('/apikey');
                    }
                }
            });

            return false;
        });
    </script>
@endsection
