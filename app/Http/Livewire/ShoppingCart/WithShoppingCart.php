<?php

namespace App\Http\Livewire\ShoppingCart;
use Carbon\Carbon;
use App\Models\Asset;
use App\Models\Loan;
use App\Models\User;

trait WithShoppingCart
{
    public $shoppingCart = [];                  #Assets user has added to the cart
    public $shoppingCost = 0;                   #Total cost of assets added to the cart
    public $avaliableEquipment = [];            #Equipment that is avaliable for booking
    public $equipmentList = [];                 #A list of all equipment in the system regardless of avaliability

    public function addItemToCart($item, $new, $itemType = 'asset')
    {
        //Livewire will convert an collection of eloquent objects into an array of arrays after the first render
        //Therefore lets convert our object into an array first so we dont need to handle both the object and
        //array version of the item
        if(is_object($item)){
            if(class_basename($item) == "asset" or class_basename($item) == "equipmentIssue" or class_basename($item) == "user"){
                if($this->checkIfItemInCart($item) == false){
                    $itemAttributeTable = $item->toArray();
                    $itemPivotAttributeTable = [];

                    if(isset($item->pivot)){
                        $itemAttributeTable['pivot'] = $item->pivot->toArray();
                        $itemAttributeTable['new'] = false;
                    }else{
                        //New asset so set returned to false
                        $itemAttributeTable['new'] = true;
                        $itemAttributeTable['pivot'] = [];

                        if($itemType == "asset"){
                            $itemAttributeTable['pivot']['returned'] = false;
                        }elseif($itemType == "issue"){
                            $itemAttributeTable['pivot']['quantity'] = 1;
                        }
                    }

                    array_push($this->shoppingCart, $itemAttributeTable);
                }elseif($itemType == 'issue'){
                    #Increment Counter for selected issue (e.g Monitor Broken)
                    $this->IncrementItemCounter($item);
                }
            }else{
                //TODO: Data is not an eloquent model of type asset
            };
        }else{
            //TODO: Data is not an object
        }
    }

    private function IncrementItemCounter($item)
    {
        foreach($this->shoppingCart as $key => $cartItem){
            if($cartItem['id'] == $item->id){
                $this->shoppingCart[$key]['pivot']['quantity'] ++;
            }
        }
    }

    private function updateItemCostInCart()
    {
        $this->shoppingCost = 0;
        foreach($this->shoppingCart as $key => $item){
            $this->shoppingCost += $item['cost'];
        }
    }

    public function removeItemFromCart($id, $decrement = false)
    {
        foreach($this->shoppingCart as $key => $cartItem){
            if($cartItem['id'] == $id){
                if($decrement == false or $cartItem['pivot']['quantity'] == 1){
                    unset($this->shoppingCart[$key]);
                }else{
                    $this->shoppingCart[$key]['pivot']['quantity'] -= 1;
                }
            }
        }
    }

    protected function checkIfItemInCart($item)
    {
        foreach($this->shoppingCart as $cartItem){
            if($cartItem['id'] == $item->id){
                return true;
            }
        }
        return false;
    }

    public function emptyCart()
    {
        $this->shoppingCart = [];
        $this->shoppingCost = 0;
    }

    public function getAllEquipment()
    {
        //Fetch all equipment in the booking system. Assume that nothing is avaliable initially

        $this->equipmentList = Asset::get()->toArray();

        foreach($this->equipmentList as $key => $equipment){
            $this->equipmentList[$key]['avaliable'] = false;
        }
    }

    public function getAllUsers()
    {
        //Fetch all users in the booking system. Assume that everything is avaliable initially

        $this->equipmentList = User::get()->toArray();

        foreach($this->equipmentList as $key => $equipment){
            $this->equipmentList[$key]['avaliable'] = true;
        }
    }

    public function getBookableUsers()
    {
        $this->getAllUsers();

        //Mark unavailable users in master equipment list
        foreach($this->shoppingCart as $cartItem){
            foreach($this->equipmentList as $key => $equipment){
                if($cartItem['id'] == $equipment['id']){
                    $this->equipmentList[$key]['avaliable'] = false;
                }
            }
        }
    }

    public function getBookableEquipment($startDate, $endDate)
    {
        $this->getAllEquipment();

        $validatedDate =[
            'start_date_time' => carbon::parse($startDate),
            'end_date_time' => carbon::parse($endDate),
            'id' =>  $this->loanId ?? -1
        ];

        //Get equipment that is avaliable for booking
        $this->avaliableEquipment = Asset::with('loans')
            ->where(function($query) use($validatedDate){
                $query->whereNotIn('assets.id', function($query) use($validatedDate){
                    $query->select('asset_loan.asset_id')
                            ->from('loans')
                            ->join('asset_loan','loans.id','asset_loan.loan_id')
                            ->whereRaw('`assets`.`id` = `asset_loan`.`asset_id`')
                            ->where('loans.id', '!=', $validatedDate['id'])
                            ->where(function($query2) use($validatedDate){
                                $query2->where('loans.start_date_time', '>=', $validatedDate['start_date_time'])
                                        ->where('loans.start_date_time', '<=', $validatedDate['end_date_time'])
                                        ->where('loans.id', '!=', $validatedDate['id']);
                            })->orWhere(function($query2) use($validatedDate){
                                $query2->where('loans.end_date_time', '>=', $validatedDate['start_date_time'])
                                    ->where('loans.end_date_time', '<=', $validatedDate['end_date_time'])
                                    ->where('loans.id', '!=', $validatedDate['id']);
                            })
                            ->where('asset_loan.returned','=',0);
                })
                ->orWhereDoesntHave('loans');
            })
            ->get(['id','name','tag'])->toArray();

        //Mark avaliable equipment in master equipment list
        foreach($this->avaliableEquipment as $equipment){
            foreach($this->equipmentList as $key => $equipment2){
                if($equipment['id'] == $equipment2['id']){
                    $this->equipmentList[$key]['avaliable'] = true;
                }
            }
        }
    }
}

