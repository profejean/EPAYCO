<div style="margin-left:20%;margin-right:30%;">
<img src="{{asset('img/.jpg')}}" alt="" style="widht:40vw;height:40vw;">
<a href="{{'http://localhost/EPAYCO/public/api/confirm_payment/'.$details['token'].'/'.$details['price']}}" style="
background:red;height:5vh;width:20vw;padding:10vw;color:white;
">Confirm amount {{$details['price']}}</a>
</div>
