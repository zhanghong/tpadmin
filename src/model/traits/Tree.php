<?php

namespace tpadmin\model\traits;

use tpadmin\model\AuthRoleRule;
use tpadmin\model\AuthRoleUser;
use tpadmin\service\auth\facade\Auth;

trait Tree
{
    /**
     * @var string
     */
    protected $parentColumn = 'parent_id';

    /**
     * @var string
     */
    protected $sortColumn = 'sort_num';

    protected $nodes;

    /**
     * Format data to tree like array.
     *
     * @return array
     */
    public function toTree(string $query_mode = self::MENU_MODE_USER)
    {
        return $this->buildNestedArray($query_mode);
    }

    public function getChildrenNodes($query_mode, $parentId, $tree = null)
    {
        if (null === $tree) {
            $tree = $this->toTree($query_mode);
        }

        foreach ($tree as $key => $value) {
            $children = isset($value['children']) ? $value['children'] : [];
            if ($value['id'] == $parentId) {
                return $children;
            }
            $this->getChildrenNodes($query_mode, $parentId, $children);
        }

        return null;
    }

    public function flatTree(string $query_mode = self::MENU_MODE_ALL, array $nodes = [])
    {
        if (empty($nodes)) {
            $nodes = $this->toTree($query_mode);
        }
        $branch = [];

        foreach ($nodes as $key => $value) {
            $value['prefix'] = '|--'.str_repeat('---', $value['depth']);
            $children = isset($value['children']) ? $value['children'] : [];
            if ($children) {
                unset($value['children']);
            }

            array_push($branch, $value);
            if ($children) {
                $branch = array_merge($branch, $this->flatTree($query_mode, $children));
            }
        }

        return $branch;
    }

    public function flatMenuTree($nodes)
    {
        if (empty($nodes)) {
            return [];
        }
        $branch = [];

        foreach ($nodes as $key => $value) {
            $children = isset($value['children']) ? $value['children'] : [];
            if ($children) {
                unset($value['children']);
            }

            $branch[$value['name']] = $value;
            if ($children) {
                $branch = array_merge($branch, $this->flatMenuTree($children));
            }
        }

        return $branch;
    }

    /**
     * Build Nested array.
     *
     * @param array $nodes
     * @param int   $parentId
     *
     * @return array
     */
    protected function buildNestedArray(string $query_mode, array $nodes = [], $parentId = 0, $ancestor_ids = [])
    {
        $branch = [];

        if (empty($nodes)) {
            if($query_mode == self::MENU_MODE_ALL){
                $nodes = $this->allNodes();
            }else{
                $nodes = $this->userNodes();
            }
        }

        foreach ($nodes as $node) {
            if ($node[$this->parentColumn] == $parentId) {
                $node['depth'] = count($ancestor_ids);
                $node['ancestor_ids'] = $ancestor_ids;
                array_push($node['ancestor_ids'], $node[$this->pk]);

                $children = $this->buildNestedArray($query_mode, $nodes, $node[$this->pk], $node['ancestor_ids']);
                if ($children) {
                    $node['children'] = $children;
                }

                $branch[] = $node;
            }
        }

        return $branch;
    }

    protected function allNodes()
    {
        return $this->order($this->sortColumn, 'ASC')->order($this->pk, 'ASC')->select()->toArray();
    }

    protected function userNodes()
    {
        $current_adminer = Auth::user();
        if(empty($current_adminer)){
            return [];
        }

        if($current_adminer->is_default){
            return $this->allNodes();
        }

        $role_ids = AuthRoleUser::where('user_id', $current_adminer->id)->column('role_id');
        if(empty($role_ids)){
            return [];
        }

        $rule_ids = AuthRoleRule::whereIn('role_id', $role_ids)->column('rule_id');
        if(empty($rule_ids)){
            return [];
        }

        return $this->whereIn($this->pk, $rule_ids)->order($this->sortColumn, 'ASC')->order($this->pk, 'ASC')->select()->toArray();
    }
}