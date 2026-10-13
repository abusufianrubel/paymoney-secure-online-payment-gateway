<?php

namespace Modules\Virtualcard\Http\Controllers\Admin;

use Exception, DB, Common;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Database\QueryException;
use Modules\Virtualcard\Entities\Virtualcard;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Virtualcard\Entities\VirtualcardCategory;
use Modules\Virtualcard\DataTables\CardCategoriesDataTable;
use Modules\Virtualcard\Http\Requests\StoreCardCategoryRequest;
use Modules\Virtualcard\Http\Requests\UpdateCardCategoryRequest;

class CardCategoriesController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }
    /**
     * Display a listing of the Card Categories.
     * @return Renderable
     */
    public function index(CardCategoriesDataTable $dataTable)
    {
        return $dataTable->render('virtualcard::admin.card_categories.index', [
            'menu'     => 'virtualcard',
            'sub_menu' => 'categories'
        ]);
    }

    /**
     * Show the form for creating a new Card Category.
     * @return Renderable
     */
    public function create()
    {
        return view('virtualcard::admin.card_categories.create', [

            'menu'     => 'virtualcard',
            'sub_menu' => 'categories'

        ]);
    }

    /**
     * Store a newly created Card Category in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(StoreCardCategoryRequest $request)
    {
        try {

            DB::beginTransaction();

            $validatedData = $request->validated();
            VirtualcardCategory::create($validatedData);

            DB::commit();

            Common::one_time_message('success', __('The :x has been successfully saved.', ['x' => __('Card Category')]));
            return redirect()->route('admin.card_categories.index');

        } catch (Exception $ex) {

            DB::rollback();
            Common::one_time_message('error', $ex->getMessage());
            return redirect()->route('admin.card_categories.index');

        }
    }

    /**
     * Show the form for editing the specified Card Category
     * @param int $id

     */
    public function edit(VirtualcardCategory $virtualcardCategory)
    {
        return view('virtualcard::admin.card_categories.edit', [

            'menu'     => 'virtualcard',
            'sub_menu' => 'categories',
            'cardCategory' => $virtualcardCategory

        ]);
    }

    /**
     * Update the specified Card Category in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(UpdateCardCategoryRequest $request, VirtualcardCategory $virtualcardCategory)
    {
        try {

            DB::beginTransaction();
            $virtualcardCategory->update($request->validated());

            DB::commit();

            Common::one_time_message('success', __('The :x has been successfully saved.', ['x' => __('Card Category')]));
            return redirect()->route('admin.card_categories.index');

        } catch (Exception $ex) {

            DB::rollback();
            Common::one_time_message('error', $ex->getMessage());
            return redirect()->route('admin.card_categories.index');

        }
    }

    /**
     * Remove the specified Card Category from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(VirtualcardCategory $virtualcardCategory)
    {
        try {

            DB::beginTransaction();
            $virtualCard = Virtualcard::where('virtualcard_category_id', $virtualcardCategory->id)->exists();

            if ($virtualCard) {
                Common::one_time_message('error', __('This :x cannot be deleted as assigned in a virtual card.', ['x' => __('Card Category')]));
                return redirect()->route('admin.card_categories.index');
            }

            $virtualcardCategory->delete();
            DB::commit();

            Common::one_time_message('success', __('The :x has been successfully deleted.', ['x' => __('Card Category')]));

        } catch (ModelNotFoundException $ex) {

            DB::rollback();
            Common::one_time_message('error', __('Requested data has not been found!'));

        } catch (QueryException $ex) {

            DB::rollback();
            Common::one_time_message('error', __('This category cannot be deleted. It is already assigned to a virtual card.'));

        } catch (Exception $ex) {

            DB::rollback();
            Common::one_time_message('error', $ex->getMessage());

        }

        return redirect()->route('admin.card_categories.index');
    }
}
