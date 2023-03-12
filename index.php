<?php
require_once './connectDB/connectdb.php';
$sql = "SELECT * FROM list_cart";
$statement = $conn->prepare($sql);
$statement->execute();
$list_cart = $statement->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['btn_add_product'])) {
        $arrKeyBtn = array_keys($_POST['btn_add_product']);
        $btnAddProductClick = reset($arrKeyBtn);
        foreach ($list_cart as $cart) {
            if (array_search($btnAddProductClick, $cart)) {
                $find_product = $cart;
                break;
            }
        }
        $name_product = $find_product['name'];
        $price_product = $find_product['price'];
        $id_product = $find_product['id'];
        $amount_product = $_POST['amount'][$btnAddProductClick];
        $total_price_product = $find_product['price'] * $_POST['amount'][$btnAddProductClick];
        $statement = $conn->prepare("SELECT * FROM cart_detail WHERE id_product = $btnAddProductClick");
        $statement->execute();
        $is_product = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($is_product)) {
            $sql_query = "UPDATE cart_detail SET amount = ?, total_price= ? WHERE id_product = $btnAddProductClick;";
            $dataSQL = [$is_product[0]['amount'] + $amount_product, ($is_product[0]['amount'] + $amount_product) * $price_product];
            $statement = $conn->prepare($sql_query);
            $statement->execute($dataSQL);
        } else {
            $sql_query = "INSERT INTO cart_detail (`name`,id_product, price, amount, total_price) VALUES (?,?,?,?,?);";
            $dataSQL = [$name_product, $id_product, $price_product, $amount_product, $total_price_product];
            $statement = $conn->prepare($sql_query);
            $statement->execute($dataSQL);
        };
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart DB</title>
    <link rel="stylesheet" href="./style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center">
        <form method="POST" action="">
            <table id="customers">
                <tr>
                    <th>STT</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th></th>
                </tr>
                <?php
                foreach ($list_cart as $index => $product) :
                ?>
                    <tr>
                        <td><?php echo $index + 1 ?></td>
                        <td><?php echo $product['name'] ?></td>
                        <td><?php echo number_format($product['price']), ' USD' ?></td>
                        <td><input type="number" name="amount[<?php echo $product['id'] ?>]" class="form-number" value="1" min="1"></td>
                        <td><button type="submit" name="btn_add_product[<?php echo $product['id'] ?>]" class="btn btn-primary">Thêm vào giỏ hàng</button></td>
                    </tr>
                <?php
                endforeach;
                ?>
            </table>
            <div class="btn-group">
                <a href="./cartDetail/cart_detail.php" class="btn btn-primary mt-3">Đi tới giỏ hàng</a>
            </div>

        </form>
    </div>
</body>

</html>