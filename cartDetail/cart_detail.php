<?php
require_once '../connectDB/connectdb.php';
session_start();
$check_desc = isset($_SESSION['list-desc']) & $_SESSION['list-desc'] === true ? 'ORDER BY id DESC' : '';
$sql = "SELECT * FROM cart_detail $check_desc ";
$statement = $conn->prepare($sql);
var_dump($_SESSION['list-desc']);
$statement->execute();
$cart_detail = $statement->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['btn-fix-product'])) {
        $arrkey = array_keys($_POST['btn-fix-product']);
        $btn_fix_clicked = reset($arrkey);
        $_SESSION['btn_fix_clicked'] = $btn_fix_clicked;
    }
    if (isset($_POST['btn-update-product'])) {
        $arrkey = array_keys($_POST['btn-update-product']);
        $btn_update_clicked = reset($arrkey);
        foreach ($cart_detail as $product) {
            if (array_search($btn_update_clicked, $product)) {
                $amount_new = $_POST['amount-update'];
                $total_price_new = $_POST['amount-update'] * $product['price'];
                break;
            }
        }
        $sql = "UPDATE cart_detail SET amount = ?, total_price = ? WHERE id=?";
        $statement = $conn->prepare($sql);
        $dataSQL = [$amount_new, $total_price_new, $btn_update_clicked];
        $statement->execute($dataSQL);
        $_SESSION['btn_fix_clicked'] = null;
        $sql = "SELECT * FROM cart_detail $check_desc";
        $statement = $conn->prepare($sql);
        $statement->execute();
        $cart_detail = $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    if (isset($_POST['btn-delete-product'])) {
        $arrkey = array_keys($_POST['btn-delete-product']);
        $btn_delete_clicked = reset($arrkey);
        $sql = "DELETE FROM cart_detail WHERE id=?";
        $statement = $conn->prepare($sql);
        $dataSQL = [$btn_delete_clicked];
        $statement->execute($dataSQL);
        $sql = "SELECT * FROM cart_detail $check_desc";
        $statement = $conn->prepare($sql);
        $statement->execute();
        $cart_detail = $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    if (isset($_POST['delete-all']) & !empty($cart_detail)) {
        $sql = "DELETE FROM cart_detail";
        $statement = $conn->prepare($sql);
        $statement->execute();
        $sql = "SELECT * FROM cart_detail $check_desc";
        $statement = $conn->prepare($sql);
        $statement->execute();
        $cart_detail = $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    if (isset($_POST['btn-up'])) {
        $_SESSION['list-desc'] = true;
        // var_dump($_SESSION['list-desc'], '1');
        // $sql = "SELECT * FROM cart_detail $check_desc";
        // $statement = $conn->prepare($sql);
        // $statement->execute();
        // $cart_detail = $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    if (isset($_POST['btn-down'])) {
        $_SESSION['list-desc'] = false;
        // var_dump($_SESSION['list-desc'], '2');
        // $sql = "SELECT * FROM cart_detail $check_desc";
        // $statement = $conn->prepare($sql);
        // $statement->execute();
        // $cart_detail = $statement->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center">
        <form method="POST">
            <table id="customers">
                <tr>
                    <th>
                        STT
                        <?php
                        isset($_SESSION['list-desc']) & $_SESSION['list-desc'] === true;
                        $check_desc_btn = $_SESSION['list-desc'] === true ? "<button  class='btn btn-warning p-1' type='submit' name='btn-down'>
                        <i class='fa-solid fa-arrow-down'></i>
                        </button>" : "<button  class='btn btn-warning p-0' type='submit' name='btn-up'>
                        <i class='mx-2 fa-solid fa-arrow-up'></i>
                        </button>";
                        echo isset($_SESSION['list-desc']) ? $check_desc_btn : "<button  class='btn btn-warning p-0' type='submit' name='btn-up'>
                        <i class='mx-2 fa-solid fa-arrow-up'></i>
                        </button>"
                        ?>

                    </th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th>Số lượng</th>
                </tr>
                <?php
                if (!empty($cart_detail)) :
                    foreach ($cart_detail as $index => $product) :
                ?>
                        <tr>
                            <td><?php echo $index + 1 ?></td>
                            <td><?php echo $product['name'] ?></td>
                            <td><?php echo number_format($product['price']), ' USD' ?></td>
                            <td>
                                <?php
                                echo $_SESSION['btn_fix_clicked'] == $product['id'] ?
                                    "<input type='number' name='amount-update' class='form-number' value=" . $product['amount'] . " min=1>" :
                                    $product['amount'];
                                ?>
                            </td>
                            <td><?php echo number_format($product['total_price']), ' USD' ?></td>
                            <td>
                                <?php
                                echo $_SESSION['btn_fix_clicked'] == $product['id'] ?
                                    " <button type='submit' name='btn-update-product[" . $product['id'] . "]' class='btn btn-primary'>Cập nhật</button>" :
                                    " <button type='submit' name='btn-fix-product[" . $product['id'] . "]' class='btn btn-primary'>Sửa</button>"
                                ?>

                                <button type="submit" name="btn-delete-product[<?php echo $product['id'] ?>]" class="btn btn-danger">Xóa</button>
                            </td>
                        </tr>
                <?php
                    endforeach;
                else : echo '<tr><td class="p-0" colspan="6"><div class="bg-danger text-white p-2 bg-opacity-50">
                Bấm vào <a class="" href="../" role="button">đây</a> để mua hàng.</div></td></tr>';
                endif;
                ?>
            </table>
            <div class="btn-group">
                <a href="../" class="btn btn-primary mt-3">Quay lại trang chủ</a>
                <button type='submit' name='delete-all' class="btn btn-danger mt-3">Xóa toàn bộ giỏ hàng</button>
            </div>
        </form>
    </div>
</body>

</html>