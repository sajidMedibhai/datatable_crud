@extends('app')
@section('content')
    <div class="container table-responsive py-5">
        <form action="{{ route('add-task') }}" id="add_task_form" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-group mx-sm-3 mb-2">
                <input type="textarea" class="form-control" id="description" name="description" placeholder="Enter Description"
                    required>
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <button type="submit" class="btn btn-primary mb-2">Ad Task</button>
            </div>
        </form>
        <br>
        <table class="table table-bordered table-hover" id="task_table" style="width: 100%">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">is_completed</th>
                    <th scope="col">description</th>
                    {{-- <th scope="col">is_completed</th> --}}
                    <th scope="col">status</th>
                    <th scope="col">action</th>
                    <th scope="col">Created At</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <button id="refresh"> Refresh</button>
    </div>

    <script>
        $(document).ready(function() {

            var Table = $("#task_table").DataTable({
                destroy: true,
                serverSide: true,
                // processing: true,
                bSortable: true,
                bRetrieve: true,
                scrollX: true,
                scrollCollapse: true,
                fixedColumns: {
                    leftColumns: 1,
                    rightColumns: 1,
                },
                iDisplayLength: 10, // per page
                language: {
                    emptyTable: "No Record Found",
                },
                ajax: $.fn.dataTable.pipeline({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    url: "/task-history-list",
                    pages: 1, // number of pages to cache
                    async: true,
                    method: "POST",
                    data: {
                        custome_filed: function() {
                            return $("#custome_filed").val();
                        },
                    },
                }),
                order: [
                    [0, "desc"]
                ],
                columnDefs: [{
                    targets: 0,
                    checkboxes: {
                        selectRow: true,
                    },
                }, ],
                select: {
                    style: "multi",
                },
                columns: [{
                        data: "sr_no",
                    },
                    {
                        data: "check_box",
                    },
                    {
                        data: "description",
                    },
                    // {
                    //     data: "is_completed",
                    // },
                    {
                        data: "status",
                    },
                    {
                        data: "action",
                    },
                    {
                        data: "created_at",
                    },
                ],
            });

            $("#refresh").click(function() {
                // loda_data();
                $.ajax({
                    url: "/task-history-list",
                    method: "POST",
                }).done(function(result) {
                    Table.clear().draw();
                    Table.rows.add(result).draw();
                })
            });
        });


        document.addEventListener("DOMContentLoaded", function(event) {

            $("#add_task_form").validate({
                ignore: ".ignoreFIle",
                rules: {
                    description: {
                        required: true,
                    },
                },
                messages: {
                    description: {
                        required: "Please enter valied Details",
                    },
                },
                highlight: function(element, errorClass, validClass) {
                    $(".remove_custome_div").remove();
                    $(element)
                        .parents("div.control-group")
                        .addClass(errorClass)
                        .removeClass(validClass);
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(".remove_custome_div").remove();
                    $(element)
                        .parents(".error")
                        .removeClass(errorClass)
                        .addClass(validClass);
                },
                submitHandler: function(form) {
                    var fd = new FormData(form);
                    $.ajax({
                        url: "/add-task",
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: "POST",
                        dataType: "json",
                        headers: {
                            "X-CSRF-TOKEN": $("input[name=_token]").val()
                        },
                        success: function(response) {
                            Swal.fire({
                                title: "Success",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK",
                            }).then((result) => {
                                // location.reload();
                                loda_data();
                                // $('#task_table').DataTable().ajax.reload();
                            });
                        },
                        error: function(xhr, status, error) {
                            var e = JSON.parse(xhr.responseText);
                            var errorCode = e.fields;
                            // append error message comes from server
                            for (x in errorCode) {
                                $("#" + x).after(
                                    "<div  class='error remove_custome_div'  id=" +
                                    x +
                                    "-error' class='error'>" +
                                    errorCode[x] +
                                    "</div>"
                                );
                            }
                            Swal.fire({
                                title: "Alert",
                                text: e.message,
                                icon: "warning",
                                confirmButtonText: "OK",
                            }).then((result) => {});
                        },
                    });
                },
            });


        });



        function loda_data() {

        }
        $(".table").on("click", ".delete_item", function() {
            var dynamicName = $(this).attr("data-name");

            var id = $(this).attr("data-delete");
            Swal.fire({
                text: "Are you sure you want to delete " + dynamicName + "?",
                icon: "warning",
                type: "warning",
                showCancelButton: !0,
                buttonsStyling: !1,
                confirmButtonText: "Yes, delete!",
                cancelButtonText: "No, cancel",
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary",
                },
            }).then(function(t) {
                if (t.value) {
                    var data_info = new FormData();
                    data_info.append("id", id);
                    $.ajax({
                        url: "/delete-task",
                        data: data_info,
                        processData: false,
                        contentType: false,
                        type: "POST",
                        dataType: "json",
                        headers: {
                            "X-CSRF-TOKEN": $("input[name=_token]").val()
                        },
                        success: function(response) {
                            Swal.fire({
                                title: "Success",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK",
                            }).then((result) => {
                                loda_data()
                            });
                        },
                        error: function(xhr, status, error) {
                            var e = JSON.parse(xhr.responseText);

                            Swal.fire({
                                title: "Alert",
                                text: e.message,
                                icon: "warning",
                                confirmButtonText: "OK",
                            }).then((result) => {});
                        },
                    });
                } else {
                    Swal.fire({
                        text: dynamicName + " was not deleted.",
                        icon: "error",
                        type: "warning",
                        buttonsStyling: !1,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        },
                    });
                }
            });
        });


        $(".table").on("click", ".changed_status", function() {
            var is_completed = $(this).attr("data-is_completed");
            is_completed = (is_completed == 1) ? 0 : 1;
            var id = $(this).attr("data-current_id");

            Swal.fire({
                text: "Are you sure you want to changed status ?",
                icon: "warning",
                type: "warning",
                showCancelButton: !0,
                buttonsStyling: !1,
                confirmButtonText: "Yes, Changed!",
                cancelButtonText: "No, cancel",
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary",
                },
            }).then(function(t) {
                if (t.value) {
                    var data_info = new FormData();
                    data_info.append("id", id);
                    data_info.append("is_completed", is_completed);
                    $.ajax({
                        url: "/chnage-task-status",
                        data: data_info,
                        processData: false,
                        contentType: false,
                        type: "POST",
                        dataType: "json",
                        headers: {
                            "X-CSRF-TOKEN": $("input[name=_token]").val()
                        },
                        success: function(response) {
                            Swal.fire({
                                title: "Success",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK",
                            }).then((result) => {
                                loda_data();
                            });
                        },
                        error: function(xhr, status, error) {
                            var e = JSON.parse(xhr.responseText);

                            Swal.fire({
                                title: "Alert",
                                text: e.message,
                                icon: "warning",
                                confirmButtonText: "OK",
                            }).then((result) => {});
                        },
                    });
                } else {
                    Swal.fire({
                        text: "status not chnaged.",
                        icon: "error",
                        type: "warning",
                        buttonsStyling: !1,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        },
                    });
                }
            });
        });
    </script>
@endsection
