<script src="js/jquery.js" type="text/javascript" language="javascript"></script>

<script language="javascript">

$(document).ready(function()
{
	$("#username").blur(function()
	{
		//remove all the class add the messagebox classes and start fading
		$("#msgbox").removeClass().addClass('messagebox').text('Verificando...').fadeIn("slow");
		//check the username exists or not from ajax
		$.post("user/user_availability.php",{ user_name:$(this).val() } ,function(data)
        {
		  if(data=='no') //if username not avaiable
		  {
		  	$("#msgbox").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
			  //add message and change the class of the box and start fading
			  $(this).html('Em uso').addClass('messageboxerror').fadeTo(900,1);
			});		
        	  }
		  else if(data=='no2') //if username not avaiable
		  {
		  	$("#msgbox").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
			  //add message and change the class of the box and start fading
			  $(this).html('Pequeno').addClass('messageboxerror').fadeTo(900,1);
			});		
        	  }
		  else if(data=='no3') //if username not avaiable
		  {
		  	$("#msgbox").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
			  //add message and change the class of the box and start fading
			  $(this).html('Grande').addClass('messageboxerror').fadeTo(900,1);
			});		
        	  }
		  else if(data=='no4') //if username not avaiable
		  {
		  	$("#msgbox").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
			  //add message and change the class of the box and start fading
			  $(this).html('Inválido').addClass('messageboxerror').fadeTo(900,1);
			});		
        	  }
		  else
		  {
		  	$("#msgbox").fadeTo(200,0.1,function()  //start fading the messagebox
			{ 
			  //add message and change the class of the box and start fading
			  $(this).html('Disponível').addClass('messageboxok').fadeTo(900,1);	
			});
		  }
				
        });
 
	});
});
</script>
<script language="javascript">

$(document).ready(function()
{
	$("#conta").blur(function()
	{
		//remove all the class add the messagebox classes and start fading
		$("#msgbox4").removeClass().addClass('messagebox').text('Verificando...').fadeIn("slow");
		//check the username exists or not from ajax
		$.post("user/acc_availability.php",{ user_name:$(this).val() } ,function(data)
        {
		  if(data=='no') //if username not avaiable
		  {
		  	$("#msgbox4").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
			  //add message and change the class of the box and start fading
			  $(this).html('Conta em uso').addClass('messageboxerror').fadeTo(900,1);
			});		
        	  }
		  else if(data=='no2') //if username not avaiable
		  {
		  	$("#msgbox4").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
			  //add message and change the class of the box and start fading
			  $(this).html('Muito pequena').addClass('messageboxerror').fadeTo(900,1);
			});		
        	  }
		  else if(data=='no3') //if username not avaiable
		  {
		  	$("#msgbox4").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
			  //add message and change the class of the box and start fading
			  $(this).html('Muito grande').addClass('messageboxerror').fadeTo(900,1);
			});		
        	  }
		  else if(data=='no4') //if username not avaiable
		  {
		  	$("#msgbox4").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
			  //add message and change the class of the box and start fading
			  $(this).html('Conta inválida').addClass('messageboxerror').fadeTo(900,1);
			});		
        	  }
		  else
		  {
		  	$("#msgbox4").fadeTo(200,0.1,function()  //start fading the messagebox
			{ 
			  //add message and change the class of the box and start fading
			  $(this).html('Conta disponível').addClass('messageboxok').fadeTo(900,1);	
			});
		  }
				
        });
 
	});
});
</script>
<script language="javascript">

$(document).ready(function()
{
	$("#emailbox").blur(function()
	{
		//remove all the class add the messagebox classes and start fading
		$("#msgbox2").removeClass().addClass('messagebox').text('Verificando...').fadeIn("slow");
		//check the emailbox exists or not from ajax
		$.post("user/email_availability.php",{ email_name:$(this).val() } ,function(data)
        {
		  if(data=='no') //if emailbox not avaiable
		  {
		  	$("#msgbox2").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
			  //add message and change the class of the box and start fading
			  $(this).html('Email em uso').addClass('messageboxerror').fadeTo(900,1);
			});		
         	 }
		  else if(data=='no2') //if emailbox not avaiable
		  {
		  	$("#msgbox2").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
			  //add message and change the class of the box and start fading
			  $(this).html('Email inválido').addClass('messageboxerror').fadeTo(900,1);
			});		
         	 }
		  else if(data=='no3') //if emailbox not avaiable
		  {
		  	$("#msgbox2").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
			  //add message and change the class of the box and start fading
			  $(this).html('Muito pequeno').addClass('messageboxerror').fadeTo(900,1);
			});		
         	 }
		  else
		  {
		  	$("#msgbox2").fadeTo(200,0.1,function()  //start fading the messagebox
			{ 
			  //add message and change the class of the box and start fading
			  $(this).html('Email válido').addClass('messageboxok').fadeTo(900,1);	
			});
		  }
				
        });
 
	});
});
</script>
<script language="javascript">

$(document).ready(function()
{
	$("#passwordbox").blur(function()
	{
		//remove all the class add the messagebox classes and start fading
		$("#msgbox3").removeClass().addClass('messagebox').text('Verificando...').fadeIn("slow");
		//check the emailbox exists or not from ajax
		$.post("user/password.php",{ pass_word:$(this).val() } ,function(data)
        {
		  if(data=='no') //if emailbox not avaiable
		  {
		  	$("#msgbox3").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
			  //add message and change the class of the box and start fading
			  $(this).html('Muito pequeno').addClass('messageboxerror').fadeTo(900,1);
			});		
         	 }
		  else if(data=='no2') //if emailbox not avaiable
		  {
		  	$("#msgbox3").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
			  //add message and change the class of the box and start fading
			  $(this).html('Senha fraca').addClass('messageboxok').fadeTo(900,1);
			});		
         	 }
		  else if(data=='no3') //if emailbox not avaiable
		  {
		  	$("#msgbox3").fadeTo(200,0.1,function() //start fading the messagebox
			{ 
			  //add message and change the class of the box and start fading
			  $(this).html('Senha normal').addClass('messageboxok').fadeTo(900,1);
			});		
         	 }
		  else
		  {
		  	$("#msgbox3").fadeTo(200,0.1,function()  //start fading the messagebox
			{ 
			  //add message and change the class of the box and start fading
			  $(this).html('Senha forte').addClass('messageboxok').fadeTo(900,1);	
			});
		  }
				
        });
 
	});
});
</script>

<style type="text/css">
.messagebox{
	position:absolute;
	width:auto;
	margin-left:20px;
	border:1px solid #c93;
	background:#ffc;
	padding:3px;
}
.messageboxok{
	position:absolute;
	width:auto;
	margin-left:20px;
	border:1px solid #349534;
	background:#C9FFCA;
	padding:3px;
	font-weight:bold;
	color:#008000;
	
}
.messageboxerror{
	position:absolute;
	width:auto;
	margin-left:20px;
	border:1px solid #CC0000;
	background:#F7CBCA;
	padding:3px;
	font-weight:bold;
	color:#CC0000;
}

</style>
