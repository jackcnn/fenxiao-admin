<?php
/**
 * Created by PhpStorm.
 * User: yangtao
 * Date: 2018/2/7
 * Time: 15:09
 */

namespace App\Services;


use App\Helpers\QueryHelper;
use App\Models\AlimamaOrder;
use App\Models\User;
use App\Models\UserOrderIncome;
use App\Models\UserTree;

class AlimamaOrderService
{
    /**
     * 订单列表
     * @param \Illuminate\Http\Request $request
     */
    public function getList($request){
        $query = UserOrderIncome::query()->from((new UserOrderIncome())->getTable() . ' as income');
        $query->leftJoin((new AlimamaOrder())->getTable() . ' as aliorder', 'aliorder.id', '=', 'income.order_id')
            ->leftJoin((new User())->getTable() . ' as user', 'user.id', '=', 'income.user_id')
            ->orderBy("create_time", "desc");

        //订单号
        if($orderNo = trim($request->get('order_no'))){
            $query->where("aliorder.order_no", $orderNo);
        }

        //商品名称
        if($goodsTitle = trim($request->get('goods_title'))){
            $query->where("aliorder.goods_title", "like", "%{$goodsTitle}%");
        }

        //订单状态
        if($orderState = trim($request->get('order_state'))){
            $query->where("aliorder.order_state", "=", $orderState);
        }

        //订单用户
        if($orderUserId = trim($request->get('order_user_id'))){
            $orderUserId = User::where("mobile", $orderUserId)->first()['id'];
            $query->where("income.order_user_id", "=", $orderUserId);
        }

        //返利用户
        if($userId = trim($request->get('user_id'))){
            $userId = User::where("mobile", $userId)->first()['id'];
            $query->where("income.user_id", "=", $userId);
        }

        //返利状态
        if($dealState = trim($request->get('deal_state'))){
            if($dealState == 1){
                $query->whereNull("aliorder.deal_time");
            }else if($dealState == 2){
                $query->whereNotNull("aliorder.deal_time");
            }
        }

        //创建时间
        if($createTimeStart = trim($request->get('create_time_start'))){
            $query->where("aliorder.create_time", ">=", $createTimeStart);
        }
        if($createTimeEnd = trim($request->get('create_time_end'))){
            $query->where("aliorder.create_time", "<=", $createTimeEnd);
        }

        //结算时间
        if($settleTimeStart = trim($request->get('settle_time_start'))){
            $query->where("aliorder.settle_time", ">=", $settleTimeStart);
        }
        if($settleTimeEnd = trim($request->get('settle_time_end'))){
            $query->where("aliorder.settle_time", "<=", $settleTimeEnd);
        }

        //返利时间
        if($dealTimeStart = trim($request->get('deal_time_start'))){
            $query->where("aliorder.deal_time", ">=", $dealTimeStart);
        }
        if($dealTimeEnd = trim($request->get('deal_time_end'))){
            $query->where("aliorder.deal_time", "<=", $dealTimeEnd);
        }


        $query->select([
            "aliorder.*",
            "user.mobile",
            "income.share_rate as user_rate"
        ]);

        $orderList = (new QueryHelper())->pagination($query);

        $userService = new UserService();

        foreach ($orderList['data'] as &$order){
            //订单状态
            $order['order_state_str'] = AlimamaOrder::getOrderStateStr($order['order_state']);
            //预估收入
            $order['predict_money'] = round($userService->getUserMoney($order['predict_money'], $order['user_rate']), 2);
            //预估结算收入
            $order['predict_income'] = round($userService->getUserMoney($order['predict_income'], $order['user_rate']), 2);
        }

        return $orderList;
    }

    /**
     * 订单详情
     * @param $orderId
     */
    public function detail($orderId){
        $order = AlimamaOrder::where("id", $orderId)->first();
        if(!$order){
            throw new \Exception("订单不存在");
        }
        //订单状态
        $order['order_state_str'] = AlimamaOrder::getOrderStateStr($order['order_state']);

        $userOrderIncome = UserOrderIncome::query()->from((new UserOrderIncome())->getTable() . ' as income')
            ->leftJoin((new User())->getTable() . ' as user', 'user.id', '=', 'income.user_id')
            ->leftJoin((new UserTree())->getTable() . ' as tree', 'user.id', '=', 'tree.user_id')
            ->where("income.order_id", $orderId)
            ->select([
                "user.id",
                "user.mobile",
                "income.order_user_id",
                "income.user_id",
                "income.share_rate as user_rate",
                "tree.grade",
            ])
            ->get();

        $userService = new UserService();


        foreach ($userOrderIncome as &$user){
            //预估收入
            $user['predict_money'] = intval($userService->getUserMoney($order['predict_money'], $user['user_rate']) * 100) / 100;
            //预估结算收入
            $user['predict_income'] = intval($userService->getUserMoney($order['predict_income'], $user['user_rate'])*100) / 100;
            $user['grade_str'] = (new UserGradeService())->getGrade($user['grade'])['grade_name'];
        }

        $data = [
            'order' => $order->toArray(),
            'users' => $userOrderIncome->toArray()
        ];

        return $data;
    }

}