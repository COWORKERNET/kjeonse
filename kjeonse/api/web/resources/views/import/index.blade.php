<!-- jQuery -->
<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.min.js" ></script>
<!-- iamport.payment.js -->
<script type="text/javascript" src="https://cdn.iamport.kr/js/iamport.payment-1.2.0.js"></script>

<script>

    window.onload = function() {
        var IMP = window.IMP;
        IMP.init("imp34478022");

        IMP.certification(
            {
                pg: '2473563486466257',
                merchant_uid: "123",
                m_redirect_url: "https://kjeonse.com",
                popup : false,

            }, function (response) {
            if(response.success) {
                console.log(response);
            }

            else {
                alert('본인인증에 실패했습니다.');
                console.log(response);
            }
        });
    }

</script>
