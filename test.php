<?php

class Company
{
    // Enter your code here
    public $id;
    public $createdAt;
    public $name;
    public $parentId;
    public $cost;
    public $children;

    public function __construct($id, $createdAt, $name, $parentId)
    {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->name = $name;
        $this->parentId = $parentId;
        $this->cost = 0;
        $this->children = [];
    }
}

class Travel
{
    // Enter your code here
    public $id;
    public $companyId;
    public $cost;

    public function __construct($id, $companyId, $cost)
    {
        $this->id = $id;
        $this->companyId = $companyId;
        $this->cost = $cost;
    }
}

class CompanyTreeBuilder
{
    private $companies;
    private $travels;

    public function __construct($companies, $travels)
    {
        $this->companies = $companies;
        $this->travels = $travels;
    }

    public function buildTree()
    {
        $rootCompanies = [];

        foreach ($this->companies as $company) {
            if ($company->parentId === "0") {
                $this->buildTreeRecursive($company);
                $rootCompanies[] = $company;
            }
        }

        return $rootCompanies;
    }
    private function calculateCost($company)
    {
        $totalCost = 0;

        foreach ($this->travels as $travel) {
            if (isset($travel->cost) && $travel->companyId === $company->id) {
                $totalCost += $travel->cost;
            }
        }

        foreach ($company->children as $child) {
            $totalCost += $child->cost;
        }

        $company->cost = $totalCost;
    }
    private function buildTreeRecursive($company)
    {
        foreach ($this->companies as $childCompany) {
            if ($childCompany->parentId === $company->id) {
                $this->buildTreeRecursive($childCompany);
                $company->children[] = $childCompany;
            }
        }

        $this->calculateCost($company);
    }


}

class TestScript
{
    public function execute()
    {
        // Enter your code here

        $companiesData = json_decode(file_get_contents('https://5f27781bf5d27e001612e057.mockapi.io/webprovise/companies'), true);
        $travelsData = json_decode(file_get_contents('https://5f27781bf5d27e001612e057.mockapi.io/webprovise/travels'), true);

        $companies = [];
        foreach ($companiesData as $companyData) {
            $companies[] = new Company(
                $companyData['id'],
                $companyData['createdAt'],
                $companyData['name'],
                $companyData['parentId']
            );
        }

        $travels = [];
        foreach ($travelsData as $travelData) {
            $travels[] = new Travel(
                $travelData['id'],
                $travelData['companyId'],
                $travelData['price']
            );
        }

        $treeBuilder = new CompanyTreeBuilder($companies, $travels);
        $result = $treeBuilder->buildTree();
     // echo json_encode($result);
        echo json_encode($result, JSON_PRETTY_PRINT);
    }
}

(new TestScript())->execute();