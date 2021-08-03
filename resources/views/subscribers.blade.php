@extends('layouts.master')

@section('title', 'API Keys')

@section('content')
    <h2>Subscribers <a class="btn btn-primary mb-4 float-right btn-form-sub" data-toggle="collapse" href="#collapseForm" role="button" aria-expanded="false" aria-controls="collapseForm">Add New Subscriber</a></h2>
    <div id="feedback"></div>

    <div class="collapse w-100 pb-3" id="collapseForm">
        <div class="card card-body w-100">
            <form id="apiForm" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" name="email" id="email" required placeholder="Enter a email address">
                </div>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" name="name" id="name" required placeholder="Enter a subscriber name">
                </div>
                <div class="form-group">
                    <label for="country">Country</label>
                    <input type="text" class="form-control" name="country" id="country" required placeholder="Enter a subscriber country">
                </div>
                <button type="submit" class="btn btn-primary float-right">Save</button>
            </form>
        </div>
    </div>

    <table class="table" id="subscriber-table">
        <thead>
            <th>Email</th>
            <th>Name</th>
            <th>Country</th>
            <th>Subscriber Date</th>
            <th>Subscriber Time</th>
            <th></th>
        </thead>
    </table>
@stop

@section('footer_scripts')
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <script src="//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script>
        var dataTable;

        $(document).ajaxError(function(e, xhr, settings, exception) {
            var msg = '';

            if('message' in xhr.responseJSON){
                msg = xhr.responseJSON.message;
            }else{
                msg = xhr.status+' '+xhr.statusText
            }

            $('#feedback').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + msg + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        });

        $(document).on('submit','#apiForm',function(event){
            event.preventDefault();

            $.ajax({
                url: '/subscribers',
                type: $("#apiForm input[name='email']").attr('readonly') ? 'PUT' : 'POST',
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
                        $('#feedback').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + xhr.responseJSON.message + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    }
                },
                success: function(result) {
                    if('error' in result && result['error'] != false && 'message' in result) {
                        $('#feedback').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + result.message + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    }else{
                        $('#feedback').html('<div class="alert alert-success alert-dismissible fade show" role="alert">' + ($("#apiForm input[name='email']").attr('readonly') ? 'The subscriber has been edited!' : 'A new subscriber has been added!') + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');

                        $('#apiForm').trigger("reset");
                        $("#apiForm input[name='email']").attr('readonly', false);

                        dataTable.ajax.reload(null, false)
                    }
                }
            });

            return false;
        });

        $(document).ready( function () {
            dataTable = $('#subscriber-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('subscribers.list') }}",
                "search": { "caseInsensitive": true },
                searchDelay: 2000,
                columns: [
                    {
                        data: 'email',
                        render: function(data, type, row) {
                            return '<a class="btn btn-link btn-edt-sub" data-id="' + row.id + '">' + row.email + '</a>';
                        },
                    },
                    { data: 'name' },
                    { data: 'country', orderable: false },
                    { data: 'subscribed_date', orderable: false },
                    { data: 'subscribed_time', orderable: false },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return '<a class="remove-image btn-rem-sub" href="#" data-email="' + row.email + '">&#215;</a>';
                        },
                    }
                ],
            });
        });

        $(document).on('click','.btn-form-sub',function(event){
            event.preventDefault();

            $('#apiForm').trigger("reset");
            $("#apiForm input[name='email']").attr('readonly', false);
        });

        $(document).on('click','.btn-rem-sub',function(event){
            event.preventDefault();

            var btn_obj = $(this);

            $.ajax({
                url: '/subscribers',
                type: 'DELETE',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "email": btn_obj.data('email')
                },
                beforeSend: function(){
                    $("#feedback").html("");
                    btn_obj.attr('disabled', 'disabled')
                },
                complete: function() {
                    btn_obj.removeAttr('disabled')
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
                        $('#apiForm').trigger("reset");

                        if ($('#collapseForm').hasClass('show')) {
                            $('#collapseForm').hide();
                        }

                        $('#feedback').html('<div class="alert alert-warning alert-dismissible fade show" role="alert">The subscriber has been deleted!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');

                        dataTable.ajax.reload(null, false);
                    }
                }
            });

            return false;
        });

        $(document).on('click','.btn-edt-sub',function(event){
            event.preventDefault();

            var tr = $(this).closest('tr');
            var dataTableRow = dataTable.row(tr[0]); // get the DT row so we can use the API on it
            var rowData = dataTableRow.data();

            $("#apiForm input[name='name']").val(rowData.name)
            $("#apiForm input[name='email']").val(rowData.email)
            $("#apiForm input[name='country']").val(rowData.country)

            $("#apiForm input[name='email']").attr('readonly', true);

            if (!$('#collapseForm').hasClass('show')) {
                $('#collapseForm').show();
            }
        });
    </script>
@endsection
