jQuery(document).ready(function ($) {
  // Send OTP button click event handler
  $("#verify-otp-btn").click(function (e) {
    e.preventDefault();
    var phoneNumber = $("#phone").val();

    $.ajax({
      url: "", // Your Request URL
      type: "POST",
      contentType: "application/json",
      data: JSON.stringify({ phone: phoneNumber }),
      success: function (response) {
        // Show the OTP input section
        console.log(response);
        $("#otp-section").show();
      },
      error: function (xhr, status, error) {
        // Handle error response
        console.error("Error sending OTP:", error);
      },
    });
  });

  // Form submission handler
  $("#verification-form").submit(function (event) {
    // Prevent default form submission
    event.preventDefault();

    // Validate required fields
    var name = $("#name").val();
    var email = $("#email").val();
    var city = $("#city").val();
    var otp =
      $("#otp1").val() + $("#otp2").val() + $("#otp3").val() + $("#otp4").val();
    var phoneNumber = $("#phone").val();

    if (!name || !email || !city || !otp) {
      // If any required field is empty, display error message
      $("#error-message").text("All fields are required.");
      return;
    }
    // Create a new FormData object
    var formData = new FormData();

    // Add form fields to FormData object
    formData.append("name", name);
    formData.append("email", email);
    formData.append("phone", phoneNumber);
    formData.append("city", city);
    formData.append("otp", otp);

    // Make AJAX request to verify OTP
    $.ajax({
      url: "", // URL Your Otp request
      type: "POST",
      contentType: "application/json",
      data: JSON.stringify({ phone: phoneNumber, otp: otp }),
      success: function (response) {
        console.log(response);
        console.log("otp verified");
        jQuery.ajax({
          url: "wp-content/plugins/custom-frm/insert.php",
          method: "POST",
          data: formData,
          contentType: false,
          processData: false,
          success: function (data) {
            console.log(data);
          },
        });
      },
      error: function (xhr, status, error) {
        // Handle error response
        console.error("Error verifying OTP:", error);
        // Optionally, display an error message to the user
      },
    });
  });
});
