<?php

namespace App\Http\Controllers\Home;

use App\Models\Order;
use App\Models\Province;
use App\Models\UserAddress;
use Cart;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'qtybutton' => 'required'
        ]);

        $product = Product::query()->findOrFail($request->product_id);
        $productVariation = ProductVariation::query()->findOrFail(json_decode($request->variation)->id);
        if ($request->qtybutton > $productVariation->quantity) {
            alert()->error('توجه!', 'تعداد محصول خواسته شده، معتبر نمی باشد!');
            return redirect()->back();
        }

        // add the product to cart
        $rowId = $product->id . '-' . $productVariation->id;
        if (Cart::get($rowId) == null) {
            Cart::add(array(
                'id' => $rowId,
                'name' => $product->name,
                'price' => $productVariation->is_sale ? $productVariation->sale_price : $productVariation->price,
                'quantity' => $request->qtybutton,
                'attributes' => $productVariation->toArray(),
                'associatedModel' => $product
            ));
        } else {
            alert()->warning('توجه!', 'این محصول قبلا به سبد خرید شما اضافه شده است!');
            return redirect()->back();
        }

        alert()->success('با تشکر', 'محصول مورد نظر به سبد خرید شما اضافه شد.');
        return redirect()->back();
    }

    public function index()
    {
        return view('home.cart.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'qtybutton' => 'required'
        ]);

        foreach ($request->qtybutton as $rowId => $quantity) {

            $item = Cart::get($rowId);
            if ($quantity > $item->attributes->quantity) {
                alert()->error('توجه!', 'تعداد محصول خواسته شده، معتبر نمی باشد!');
                return redirect()->back();
            }
            Cart::update($rowId, array(
                'quantity' => array(
                    'relative' => false,
                    'value' => $quantity
                ),
            ));
        }
        alert()->success('با تشکر', 'سبد خرید ویرایش شد.');
        return redirect()->back();

    }

    public function remove($rowId)
    {
        Cart::remove($rowId);
        alert()->success('با تشکر', 'محصول مورد نظر از سبد خرید شما حذف شد.');
        return redirect()->back();
    }

    public function clear()
    {
        Cart::clear();
        alert()->warning('با تشکر', 'سبد خرید شما خالی شد!');
        return redirect()->back();
    }

    public function checkCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);
        if(!auth()->check()){
            alert()->error('توجه!', 'برای استفاده از کد تخفیف، نیاز است ابتدا وارد سایت شوید!');
            return redirect()->back();
        }
        $result = checkCoupon($request->code);
        if(array_key_exists('error', $result)){
        alert()->error('توجه!', $result['error']);
        }else{
            alert()->success('با تشکر', $result['success']);
        }
        return redirect()->back();
    }

    public function checkout()
    {
        if(\Cart::isEmpty()){
            alert()->warning('توجه!', 'سبد خرید شما خالی است!');
            return redirect()->route('home.index');
        }
        $addresses = UserAddress::query()->where('user_id', auth()->id())->get();
        $provinces = Province::all();

        return view('home.cart.checkout', compact('addresses', 'provinces'));
    }

    public function usersProfileIndex()
    {
        $orders = Order::query()->where('user_id', auth()->id())->get();
        return view('home.users_profile.orders', compact('orders'));
    }
}
