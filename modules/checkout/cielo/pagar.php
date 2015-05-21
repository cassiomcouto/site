<?php

  
  include("../../../init.php");
  $urls     = $GLOBALS['ShopPath'];
  $nomeloja = $GLOBALS['StoreName'];
  global $itemId;
  $tk      = $_GET['token'];
  $a       = explode("#", base64_decode($tk));
  $cartao  = $a[0];
  $itemId  = ( integer ) $a[1];
  $tipocc  = $a[2];
  $parcela = ( integer ) $a[3];
  $hash    = $a[4];
  echo "<br><br><br><br><br><center><h2>Aguarde...</h2></center>";
  require("includes/include.php");
  $afiliacao    = GetModuleVariable("checkout_cielo", "afiliacao");
  $chave        = GetModuleVariable("checkout_cielo", "chave");
  $modo         = GetModuleVariable("checkout_cielo", "modo");
  $juros        = GetModuleVariable("checkout_cielo", "juros");
  $desconto     = GetModuleVariable("checkout_cielo", "desconto");
  $tipojuros    = GetModuleVariable("checkout_cielo", "tipojuros");
  $semjurosate  = GetModuleVariable("checkout_cielo", "jurosde");
  $cap          = GetModuleVariable("checkout_cielo", "captura");
  $getovalor    = TotalPedidoCielo($itemId);
  $valorreal    = number_format($getovalor, 2, "", "");
  
  if ($tipocc == 2 && $semjurosate < $parcela) {
      $valordaparcela = parcelarcielo($getovalor, $juros, $parcela);
      $valor          = number_format($valordaparcela * $parcela, 2, "", "");
  } else if ($tipocc == 3) {
      $valordaparcela = parcelarcielo($getovalor, $juros, $parcela);
      $valor          = number_format($valordaparcela * $parcela, 2, "", "");
  } else if ($tipocc == "A" && 0 < $desconto) {
      $vald  = $getovalor / 100 * $desconto;
      $valor = number_format($getovalor - $vald, 2, "", "");
  } else {
      $valor = $valorreal;
  }
  if ($modo == "T") {
      $urlvisa = TESTE;
  } else {
      $urlvisa = PRODUCAO;
  }
  define("ENDERECO", $urlvisa);
  $Pedido                         = new Pedido();
  $Pedido->formaPagamentoBandeira = $op;
  if ($_POST['formaPagamento'] != "A" && $parcela != "1") {
      $Pedido->formaPagamentoProduto  = $tipocc;
      $Pedido->formaPagamentoParcelas = $parcela;
  } else {
      $Pedido->formaPagamentoProduto  = $tipocc;
      $Pedido->formaPagamentoParcelas = 1;
  }
  $Pedido->dadosEcNumero     = $afiliacao;
  $Pedido->dadosEcChave      = $chave;
  $Pedido->capturar          = "false";
  $Pedido->autorizar         = $autorizar;
  $Pedido->dadosPedidoNumero = $itemId;
  $Pedido->dadosPedidoValor  = $valor;
  $Pedido->urlRetorno        = $urls . "/modules/checkout/cielo/recibo.php?id=" . base64_encode($itemId);
  $objResposta               = $Pedido->RequisicaoTransacao(false);
  if (!empty($objResposta->tid)) {
      $Pedido->tid             = $objResposta->tid;
      $Pedido->pan             = $objResposta->pan;
      $Pedido->status          = $objResposta->status;
      $urlAutenticacao         = "url-autenticacao";
      $Pedido->urlAutenticacao = $objResposta->$urlAutenticacao;
      $tid                     = $objResposta->tid;
      $total                   = @$GLOBALS['ISC_CLASS_DB']->Query("SELECT COUNT(id) as total FROM cielo WHERE pedido ='" . $itemId . "'");
      $total                   = @$GLOBALS['ISC_CLASS_DB']->Fetch($total);
      if ($total['total'] == 0) {
          $sql = "INSERT INTO cielo (id , pedido, valor, tid, auth, data, cc) VALUES (\r\nNULL , '" . $itemId . "', '" . $valor . "', '" . $tid . "', '" . $hash . "', '" . time() . "', '" . $op . "');";
          @$GLOBALS['ISC_CLASS_DB']->Query($sql);
      } else if ($total['total'] == 1) {
          @$GLOBALS['ISC_CLASS_DB']->Query("UPDATE cielo SET valor=\"" . $valor . "\",tid=\"" . $tid . "\",auth=\"" . $hash . "\",data=\"" . @time() . "\",cc=\"" . $op . "\" WHERE pedido =\"" . $itemId . "\"");
      }
      echo "<script type=\"text/javascript\">window.location.href=\"" . $Pedido->urlAutenticacao . "\"</script>";
  } else {
      echo "Ocorreu um erro ao solititar o pagamento, verifique se o ambiente esta correto para a afiliacao usada e se a afiliacao e chave usada estao corretas.<br>Log:</br>";
      print_r($objResposta);
  }
  echo "\r\n";
?>