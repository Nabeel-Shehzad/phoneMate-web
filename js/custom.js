// data tables

$.fn.dataTable.ext.errMode = 'none';

(function( $ ) {

    'use strict';

    var datatableInit = function() {

        $('#my_listed_items').dataTable({
            "pageLength": 5
        });

    };

    $(function() {
        datatableInit();
    });

}).apply( this, [ jQuery ]);

(function( $ ) {

    'use strict';

    var datatableInit = function() {

        $('#my_sold_items').dataTable({
            "pageLength": 5
        });

    };

    $(function() {
        datatableInit();
    });

}).apply( this, [ jQuery ]);

(function( $ ) {

    'use strict';

    var datatableInit = function() {

        $('#my_rejected_items').dataTable({
            "pageLength": 5
        });

    };

    $(function() {
        datatableInit();
    });

}).apply( this, [ jQuery ]);

(function( $ ) {

    'use strict';

    var datatableInit = function() {

        $('#payments_profit_and_gross').dataTable({
            "pageLength": 5
        });

    };

    $(function() {
        datatableInit();
    });

}).apply( this, [ jQuery ]);





// to get current year
function getYear() {
    var currentDate = new Date();
    var currentYear = currentDate.getFullYear();
    document.querySelector("#displayYear").innerHTML = currentYear;
}

getYear();


/** google_map js **/
function myMap() {
    var mapProp = {
        center: new google.maps.LatLng(40.712775, -74.005973),
        zoom: 18,
    };
    var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
}




// Design code for the form
$(document).ready(function() {
    $('#contact_form').bootstrapValidator({
        // To use feedback icons, ensure that you use Bootstrap v3.1.0 or later
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            first_name: {
                validators: {
                        stringLength: {
                        min: 2,
                    },
                        notEmpty: {
                        message: 'Please supply your first name'
                    }
                }
            },
             last_name: {
                validators: {
                     stringLength: {
                        min: 2,
                    },
                    notEmpty: {
                        message: 'Please supply your last name'
                    }
                }
            },
            email: {
                validators: {
                    notEmpty: {
                        message: 'Please supply your email address'
                    },
                    emailAddress: {
                        message: 'Please supply a valid email address'
                    }
                }
            },
            phone: {
                validators: {
                    notEmpty: {
                        message: 'Please supply your phone number'
                    },
                    phone: {
                        country: 'US',
                        message: 'Please supply a vaild phone number with area code'
                    }
                }
            },
            address: {
                validators: {
                     stringLength: {
                        min: 8,
                    },
                    notEmpty: {
                        message: 'Please supply your street address'
                    }
                }
            },
            city: {
                validators: {
                     stringLength: {
                        min: 4,
                    },
                    notEmpty: {
                        message: 'Please supply your city'
                    }
                }
            },
            state: {
                validators: {
                    notEmpty: {
                        message: 'Please select your state'
                    }
                }
            },
            zip: {
                validators: {
                    notEmpty: {
                        message: 'Please supply your zip code'
                    },
                    zipCode: {
                        country: 'US',
                        message: 'Please supply a vaild zip code'
                    }
                }
            },
            comment: {
                validators: {
                      stringLength: {
                        min: 10,
                        max: 200,
                        message:'Please enter at least 10 characters and no more than 200'
                    },
                    notEmpty: {
                        message: 'Please supply a description of your project'
                    }
                    }
                }
            }
        })
        .on('success.form.bv', function(e) {
            $('#success_message').slideDown({ opacity: "show" }, "slow") // Do something ...
                $('#contact_form').data('bootstrapValidator').resetForm();

            // Prevent form submission
            e.preventDefault();

            // Get the form instance
            var $form = $(e.target);

            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');

            // Use Ajax to submit form data
            $.post($form.attr('action'), $form.serialize(), function(result) {
                console.log(result);
            }, 'json');
        });
});


//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
// code for the image select and view on the list item form
// 
document.getElementById('uploadButton').addEventListener('click', function() {
    document.getElementById('fileInput').click();
});

document.getElementById('deleteButton').addEventListener('click', function() {
    document.getElementById('fileInput').value = '';
    document.getElementById('imagePreview').src = 'https://via.placeholder.com/100';
});

document.getElementById('deleteButtonC').addEventListener('click', function() {
    var img = document.getElementById('image-holder').value;
    var path = './uploads/item-images/'+img;
    var src = document.getElementById('imagePreview').src;
    if (src == path) {
        document.getElementById('fileInput').value = img;
        document.getElementById('imagePreview').src = 'https://via.placeholder.com/100';
    } else {
        document.getElementById('fileInput').value = img;
        document.getElementById('imagePreview').src = path;
    }
});

function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById('imagePreview');
        output.src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}
//
// code for the image select and view on the list item form
//