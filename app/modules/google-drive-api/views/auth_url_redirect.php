<p>You have successfully logged in.</p>
<p>You will be redirected in <span id="redirect_count">5</span></p>
<script>
  (function(){
    var redirect_count = document.getElementById ( 'redirect_count' )
    var seconds = redirect_count.innerHTML;
    while ( seconds > 0 ){
      seconds--;
      redirect_count.innerHTML = seconds;
    }
  })();
</script>
