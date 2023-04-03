<?php
/**
 * Created by LJL.
 * Date: 2022/3/17
 * Time: 12:36
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;

class Common extends Model
{

    public function paginate($perPage = null, $columns = ['*'], $page = null, $pageName = 'page', $where)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $perPage = $perPage ?: $this->model->getPerPage();

        $results = ($total = $this->toBase()->where($where)->getCountForPagination()) ? $this->forPage($page, $perPage)->where($where)->orderBy('id', 'desc')->get($columns)->toArray() : [];
        $pages   = ceil($total / $perPage);

        return [
            'total'        => $total,
            'current_page' => $page,
            'page_size'    => $perPage,
            'pages'        => $pages,
            'data'         => $results
        ];
    }

}
