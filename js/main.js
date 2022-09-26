var app = new Vue({
  el: "#app_page",
  data: {
    numCard: "",
    cardHoldName: "",
    expDateMonth: "",
    expDateYear: "",
    csv: "",
    plan: 0,
    email:'',
    phone:'',
  },
  methods: {
    payment: function () {
      this.valideteForm();
      OpenPay.token.create(
        {
          card_number: this.numCard,
          holder_name: this.cardHoldName,
          expiration_year: this.expDateYear,
          expiration_month: this.expDateMonth,
          cvv2: this.csv,
        },
        this.onSuccess,
        this.onError
      );
    },
    onSuccess : function(response){
      let post_data = {
        token: response.data.id,
        device_session_id: $('#deviceIdHiddenFieldName').val()
      };
      this.$http.post('./app.php',JSON.stringify(post_data)).then(
        function (response){
          console.log(response.body);
        },
        function (err){
            console.log(err);
        }
      );
    },
    onError : function(response){
      console.error(response);
    },
    valideteForm: function () {
      return true;
    },
  },
  created: function () {},
  mounted: function () {},
});

$(document).ready(function () {
  OpenPay.setId("mmey69zmnv0ub7zhny23");
  OpenPay.setApiKey("pk_234a0463f2a94c459334aea543092455");
  OpenPay.setSandboxMode(true);
  var deviceSessionId = OpenPay.deviceData.setup(
    "payment-form",
    "deviceIdHiddenFieldName"
  );
  console.log(deviceSessionId);
});
