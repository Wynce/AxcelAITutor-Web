 function ConfirmDeleteFunction(url, id = false) {
  var message = "You will not be able to recover this record again!";
  swal({
      title: "Are you sure?",
        text: message,
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel",
        closeOnConfirm: true,
        closeOnCancel: true
    },
    function(isConfirm) {
      if (isConfirm) {
        location.href=url;
        return true;
      } else {
        return false;
      }
    });
}


/*function to change status*/
function ConfirmStatusFunction(url, id = false) {
  var message = "You want to change the status?";
  swal({
      title: "Are you sure?",
        text: message,
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-success",
        //cancelButtonClass:"btn-danger",
        confirmButtonText: "Yes, change it!",
        cancelButtonText: "No, cancel",
        closeOnConfirm: true,
        closeOnCancel: true
    },
    function(isConfirm) {
      if (isConfirm) {
        location.href=url;
        return true;
      } else {
        return false;
      }
    });
}
