<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Database\Expression\IdentifierExpression;
use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Network\Session\DatabaseSession;
use Cake\Datasource\ConnectionManager;
use Cake\Database\Connection;
use Cake\I18n\Time;

class GymBranchController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent("GYMFunction");
        $session = $this->request->session()->read("User");
        $this->set("session", $session);
    }

    public function branchList()
    {
        $session = $this->request->session()->read("User");
        if ($session["role_name"] == "administrator") {
            $data = $this->GymBranch->find("all")->hydrate(false)->toArray();
        } else {
            // Non-admin users can only view their assigned branch
            $data = $this->GymBranch->find("all")
                ->where(["id" => $session["branch_id"]])
                ->hydrate(false)
                ->toArray();
        }
        $this->set("data", $data);
    }

    public function addBranch()
    {
        $this->set("edit", false);
        $this->set("title", __("Add Branch"));
        
        $branch = $this->GymBranch->newEntity();
        
        if ($this->request->is("post")) {
            $this->request->data['created_date'] = date("Y-m-d");
            $this->request->data['is_active'] = 1;
            
            $branch = $this->GymBranch->patchEntity($branch, $this->request->data);
            
            if ($this->GymBranch->save($branch)) {
                $this->Flash->success(__("Success! Branch Added Successfully."));
                return $this->redirect(["action" => "branchList"]);
            } else {
                if ($branch->errors()) {
                    foreach ($branch->errors() as $error) {
                        foreach ($error as $key => $value) {
                            $this->Flash->error(__($value));
                        }
                    }
                }
            }
        }
    }

    public function editBranch($id)
    {
        $this->set("edit", true);
        $this->set("title", __("Edit Branch"));
        
        $session = $this->request->session()->read("User");
        if ($session["role_name"] != "administrator") {
            $this->Flash->error(__("Access Denied! Only administrators can edit branches."));
            return $this->redirect(["action" => "branchList"]);
        }
        
        $data = $this->GymBranch->get($id)->toArray();
        $this->set("data", $data);
        
        if ($this->request->is("post")) {
            $row = $this->GymBranch->get($id);
            $update = $this->GymBranch->patchEntity($row, $this->request->data);
            
            if ($this->GymBranch->save($update)) {
                $this->Flash->success(__("Success! Branch Updated Successfully."));
                return $this->redirect(["action" => "branchList"]);
            } else {
                if ($update->errors()) {
                    foreach ($update->errors() as $error) {
                        foreach ($error as $key => $value) {
                            $this->Flash->error(__($value));
                        }
                    }
                }
            }
        }
        $this->render("addBranch");
    }

    public function deleteBranch($id)
    {
        $session = $this->request->session()->read("User");
        if ($session["role_name"] != "administrator") {
            $this->Flash->error(__("Access Denied! Only administrators can delete branches."));
            return $this->redirect(["action" => "branchList"]);
        }
        
        // Don't allow deleting the central branch (id=1)
        if ($id == 1) {
            $this->Flash->error(__("Cannot delete the Central Branch!"));
            return $this->redirect(["action" => "branchList"]);
        }
        
        // Check if branch has any members
        $memberCount = $this->GymBranch->GymMember->find()
            ->where(['branch_id' => $id])
            ->count();
            
        if ($memberCount > 0) {
            $this->Flash->error(__("Cannot delete branch with active members. Please reassign members first."));
            return $this->redirect(["action" => "branchList"]);
        }
        
        $row = $this->GymBranch->get($id);
        if ($this->GymBranch->delete($row)) {
            $this->Flash->success(__("Success! Branch Deleted Successfully."));
        }
        return $this->redirect(["action" => "branchList"]);
    }

    public function viewBranch($id)
    {
        $session = $this->request->session()->read("User");
        if ($session["role_name"] != "administrator" && $session["branch_id"] != $id) {
            $this->Flash->error(__("Access Denied!"));
            return $this->redirect(["action" => "branchList"]);
        }
        
        // Get branch details with statistics using a single optimized query
        $data = $this->GymBranch->find()
            ->where(['GymBranch.id' => $id])
            ->select([
                'GymBranch.id',
                'GymBranch.name',
                'GymBranch.address',
                'GymBranch.phone',
                'GymBranch.email',
                'GymBranch.notes',
                'GymBranch.created_date',
                'GymBranch.is_active',
                'total_members' => $this->GymBranch->GymMember->find()
                    ->where([
                        'GymMember.branch_id' => new IdentifierExpression('GymBranch.id'),
                        'GymMember.role_name' => 'member'
                    ])
                    ->select(['count' => 'COUNT(*)'])
                    ->group('GymMember.branch_id'),
                'active_members' => $this->GymBranch->GymMember->find()
                    ->where([
                        'GymMember.branch_id' => new IdentifierExpression('GymBranch.id'),
                        'GymMember.role_name' => 'member',
                        'GymMember.membership_status' => 'Continue'
                    ])
                    ->select(['count' => 'COUNT(*)'])
                    ->group('GymMember.branch_id'),
                'total_staff' => $this->GymBranch->GymMember->find()
                    ->where([
                        'GymMember.branch_id' => new IdentifierExpression('GymBranch.id'),
                        'GymMember.role_name' => 'staff_member'
                    ])
                    ->select(['count' => 'COUNT(*)'])
                    ->group('GymMember.branch_id')
            ])
            ->first();
            
        $this->set("data", $data);
        
        // Get recent activities for this branch
        $activities = $this->GymBranch->GymMember->GymAttendance->find()
            ->contain(['GymMember'])
            ->where([
                'GymAttendance.branch_id' => $id
            ])
            ->order(['GymAttendance.attendance_date' => 'DESC'])
            ->limit(10)
            ->toArray();
            
        $this->set("activities", $activities);
    }

    public function isAuthorized($user)
    {
        $role = $user["role_name"];
        $curr_action = $this->request->action;
        $members_actions = ["branchList", "viewBranch"];
        $staff_acc_actions = ["branchList", "viewBranch"];
        $acc_actions = ["branchList", "viewBranch"];
        
        if ($role == "member" && in_array($curr_action, $members_actions)) {
            return true;
        }
        
        if ($role == "staff_member" && in_array($curr_action, $staff_acc_actions)) {
            return true;
        }
        
        if ($role == "accountant" && in_array($curr_action, $acc_actions)) {
            return true;
        }
        
        if ($role == "administrator") {
            return true;
        }
        
        return parent::isAuthorized($user);
    }
}