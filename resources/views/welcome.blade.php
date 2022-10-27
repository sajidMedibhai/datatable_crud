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
        <div class="table-responsive hidden">

            <table class="table table-bordered table-striped table-hover" id="task_table" style="width: 100%">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Completed</th>
                        <th scope="col">description</th>
                        {{-- <th scope="col">is_completed</th> --}}
                        {{-- <th scope="col">Status</th> --}}
                        <th scope="col">Action</th>
                        <th scope="col">Created At</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="hidden">
            <button id="refresh"> Refresh</button>
            <button id="load-dt"> load-dt</button>
        </div>

    </div>

    <script>
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
                                $("#refresh").click();
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


        $(document).ready(function() {

            $("#load-dt, #refresh").click(function() {

                $.ajax({
                    url: "/task-history-list",
                    method: "POST",
                }).done(function(result) {
                    animal_table.clear().draw();
                    animal_table.rows.add(result).draw();
                });

            });

            if ($(".table-responsive").hasClass("hidden")) {
                $(".table-responsive").removeClass("hidden");
                $.ajax({
                    url: "/task-history-list",
                    method: "POST",
                }).done(function(result) {
                    animal_table.clear().draw();
                    animal_table.rows.add(result).draw();
                });
            }


            let animal_table = $("#task_table").DataTable({
                pageLength: 10,
                lengthMenu: [10, 20, 30, 50, 75, 100],
                order: [],
                paging: true,
                searching: true,
                info: true,
                data: [],
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
                    // {
                    //     data: "status",
                    // },
                    {
                        data: "action",
                    },
                    {
                        data: "created_at",
                    },
                ],
            });
        });



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
                                $("#refresh").click();
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
                    $("#refresh").click();
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
        });
    </script>
@endsection
