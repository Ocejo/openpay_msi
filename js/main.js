var app = new Vue({
  el: "#app_page",
  data: {
    numCard: "",
    cardHoldName: "",
    expDateMonth: "",
    expDateYear: "",
    csv: "",
    plan: 0,
    isDisabled: false,
    btnText:'Pagar',
    product:[],
    productText:'none',
    mountText:'$0',
    mount:0,
    plan:'',
    plans:[],
    customer: {
      name: "",
      lastName:"",
      email: "",
      phone: "",
    }
  },
  methods: {
    payment: function () {
      this.btnPagar('p');
      if(!this.valideteForm()){
        this.btnPagar('c');
        Swal.fire("Error", 'Campos Requeridos', "error");
        return true;
      }
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
    onSuccess: function (response) {
      this.btnPagar('p');
      const that = this;
      let post_data = {
        token: response.data.id,
        device_session_id: $("#deviceIdHiddenFieldName").val(),
        plan: that.plan,
        mount: that.mount,
        customer: that.customer,
        security: (this.mount > 5000) ? true : false,
      };
      this.$http.post("./app.php", JSON.stringify(post_data)).then(
        function (response) {
          that.btnPagar('c');
          if(response.body.success){
            Swal.fire("Correcto", response.body.message, "success");
            if(response.body.url!='') window.location.href = response.body.url;
          }else{
            Swal.fire("Error", response.body.message, "error");
          }
        },
        function (err) {
          this.btnPagar('c');
          console.error(err);
          Swal.fire("Error", err.body, "error");
        }
      );
    },
    onError: function (response) {
      this.btnPagar('c');
      Swal.fire("Error", response.data.description, "error");
      console.error(response.data);
    },
    valideteForm: function () {
      var validate = true;
      if(this.plan==''){
        validate = false
      }
      return validate;
    },
    btnPagar: function (status) {
      const that = this,
            btnStatus = {
              c: function() {
                that.isDisabled = false;
                that.btnText = 'Pagar';
              },
              p: function() {
                that.isDisabled = true;
                that.btnText = 'Pagando...';
              }
            };
      btnStatus[status]();
    },
    descript: function(str){
      switch(str) {
      case '4e07408562bedb8b60ce05c1decfe3ad16b72230967de01f640b7e4729b49fce':
          return 3
          break;
      case 'e7f6c011776e8db7cd330b54174fd76f7d0216b612387a5ffcfb81e6f0919683':
          return 6
          break;
      case '19581e27de7ced00ff1ce50b2047e7a567c76b1cbaebabe5ef03f7c3017bb5b7':
          return 9
          break;
      case '6b51d431df5d7f141cbececcf79edf3dd861c3b4069f0b11661a3eefacbba918':
          return 12
          break;
      }
  }
  },
  created: function () {
    const params = new URLSearchParams(location.search),
        monto = params.get('mount'),
        ref = params.get('ref'),
        product = params.get('product'),
        plans = params.get('plans'),
        that = this;
    if(monto==null||product==null||plans==null){
      Swal.fire("Error", 'Parametros necesarios', "error");
      that.btnPagar('c');
    }else{
      let planArr = plans.split(',');
      planArr.forEach((plan, idx) => {
          if (idx == 0) that.plan = that.descript(plan);
          that.plans.push({text:that.descript(plan)+' Meses', value:that.descript(plan)});
      });
      that.productText = product;
      that.mountText = '$'+monto;
      that.mount = parseFloat(monto);
    }
  },
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
});
