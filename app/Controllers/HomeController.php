<?php
namespace App\Controllers;

use App\Controllers\BaseController;

use \App\Models\Admin\AdminSubcategory;

use \App\Models\Admin\AdminProduct;

use Symfony\Component\Translation\Translator;

use Symfony\Component\Translation\Loader\PhpFileLoader;

/**
 * Description of HomeController
 *
 * @author Comtech
 */
class HomeController extends BaseController 
{
    private $data;
    
    public function translation($request, $response, $args)
    {
        // Languages setup
        try {
            $locale = $args['locale'];
            $translation_path = __DIR__ . '\\..\\..\\translations';
            //$translation_path = __DIR__ . '/../../translations'; // Linux
            if($locale === 'en') {
                $files = '\messages.en.php';
                //$files = '/messages.en.php'; // Linux
                $form = 'en_GB';
            } else {
                $files = '\messages.gr.php';
                //$files = '/messages.gr.php'; // Linux
                $form = 'el_GR';
            }
            $translation_path_file = $translation_path . $files;

            $trans = new Translator($locale);
            $trans->setFallbackLocales(array('el_GR'));
            $trans->addLoader('php', new PhpFileLoader());
            $trans->addResource('php', $translation_path_file, $form);
            $language_catalogue = $trans->getCatalogue($form);
            $translation_messages = $language_catalogue->all();

            if($translation_messages) {
                return $translation_messages;                
            }
        } catch (Exception $ex) {
            $this->c->errorLogger->addError("Error Code: " . $ex->getCode() . " - File: " . $ex->getFile() . " - Line: " . $ex->getLine(), array($ex->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $ex->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        } catch (\Error $er) {
            $this->c->errorLogger->addError("Error Code: " . $er->getCode()  . " - File: " . $er->getFile() . " - Line: " . $er->getLine(), array($er->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $er->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        } 
        // End Languages Setup
    }


    public function index($request, $response, $args) 
    {
        try {
            $locale = $args['locale'];
            $translation = $this->translation($request, $response, $args);

            $prod_path = $this->c->get('product_upload_directory');

            $products = AdminProduct::join('categories', 'products.product_category_id', '=', 'categories.id')->
                    join('subcategories', 'products.product_subcategory_id', '=', 'subcategories.id')->
                    select('product_title', 'product_price', 'product_foto', 'product_slug')->
                    where('subcategories.subcategory_active', '=', '1')->
                    where('categories.category_active', '=', '1')->
                    get();

            $active_featured_products = $this->getProducts($request, $response);
            $featured_length = count($active_featured_products);
            //var_dump($featured_length);
            for($i = 0; $i < $featured_length; $i++) {
                $featured_product_title[$i] = unserialize($active_featured_products[$i]->product_title);
             }

            $prod_length = count($products);

            for($i = 0; $i < $prod_length; $i++) {
                $productTitleUnserialized[$i] = unserialize($products[$i]->product_title);
                $product_price[$i] = $products[$i]->product_price;
                $product_foto[$i] = $products[$i]->product_price;
                $product_slug[$i] = $products[$i]->product_slug;
            }

            $categories = $this->getCategories($request, $response);
            $cat_length = count($categories);
            for($i = 0; $i < $cat_length; $i++) {
                $categoryTitlesUnserialized[$i] = unserialize($categories[$i]->category_title);
                $categoryDescriptionsUnserialize[$i] = unserialize($categories[$i]->category_desc);
            }

            if(empty($this->getLogoImg())) {
                return $this->c->view->render($response, '/comtechbs/user_home.twig', [
                    'page_title' => $this->getPageTitle(),
                    'logo_name' => $this->getPageLogoName(),
                    'path' => '',
                    'categories' => $categories,
                    'cat_titles' => $categoryTitlesUnserialized,
                    'cat_descs' => $categoryDescriptionsUnserialize,
                    'products' => $products,
                    'carousel_products' => $active_featured_products,
                    'carousel_length' => $featured_length,
                    'carousel_product_title' => $featured_product_title,
                    'prod_titles' => $productTitleUnserialized,
                    'prod_price' => $product_price,
                    'prod_foto' => $product_foto,
                    'prod_slug' => $product_slug,
                    'product_path' => $prod_path,
                    'locale' => $locale,
                    'messages' => $translation,
                    'cat_size' => $cat_length,
                    'prod_size' => $prod_length,
                ]);
            }
            return $this->c->view->render($response, '/comtechbs/user_home.twig', [
                'page_title' => $this->getPageTitle(),
                'logo_name' => $this->getPageLogoName(),
                'logo_img' => $this->getLogoImg(),
                'path' => '/restaurant-slim-v1/public/img/logo/',
                //'path' => '/restaurant-v1_1/public/img/logo/', // linux
                'categories' => $categories,
                'cat_titles' => $categoryTitlesUnserialized,
                'cat_descs' => $categoryDescriptionsUnserialize,
                'products' => $products,
                'carousel_products' => $active_featured_products,
                'carousel_product_title' => $featured_product_title,
                'carousel_length' => $featured_length,
                'prod_titles' => $productTitleUnserialized,
                'prod_price' => $product_price,
                'prod_foto' => $product_foto,
                'prod_slug' => $product_slug,
                'product_path' => $prod_path,
                'locale' => $locale,
                'messages' => $translation,
                'cat_size' => $cat_length,
                'prod_size' => $prod_length,
            ]); 
        } catch (\Illuminate\Database\QueryException $qex) {
            $this->c->errorLogger->addError("Error Code: " . $qex->getCode() . " - File: " . $qex->getFile() . " - Line: " . $qex->getLine(), array($qex->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $qex->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        } catch (Exception $ex) {
            $this->c->errorLogger->addError("Error Code: " . $ex->getCode() . " - File: " . $ex->getFile() . " - Line: " . $ex->getLine(), array($ex->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $ex->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        } catch (\Error $er) {
            $this->c->errorLogger->addError("Error Code: " . $er->getCode()  . " - File: " . $er->getFile() . " - Line: " . $er->getLine(), array($er->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $er->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        } 
    }
    
    public function getPageData()
    {
        try {
            $this->data = BaseController::getAllOrderBy('pages', 'id', 'asc');
            if($this->data) {
                return $this->data[0];
            }            
        } catch (\Illuminate\Database\QueryException $ex) {
            $this->c->errorLogger->addError("Error Code: " . $ex->getCode() . " - File: " . $ex->getFile() . " - Line: " . $ex->getLine(), array($ex->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $ex->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        } catch (\Error $er) {
            $this->c->errorLogger->addError("Error Code: " . $er->getCode()  . " - File: " . $er->getFile() . " - Line: " . $er->getLine(), array($er->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $er->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        }
    }
    
    public function getPageTitle() 
    {
        try {
            $title = $this->getPageData();
            $titleUnserialized = unserialize($title->page_title);

            return $titleUnserialized;
        } catch (Exception $ex) {
            $this->c->errorLogger->addError("Error Code: " . $ex->getCode() . " - File: " . $ex->getFile() . " - Line: " . $ex->getLine(), array($ex->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $ex->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        } catch (\Error $er) {
            $this->c->errorLogger->addError("Error Code: " . $er->getCode()  . " - File: " . $er->getFile() . " - Line: " . $er->getLine(), array($er->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $er->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        }   
    }
    
    public function getPageLogoName()
    {
        try {
            $logo_name = $this->getPageData();
            $logonameUnserialized = unserialize($logo_name->logo_name);

            return $logonameUnserialized;
        } catch (Exception $ex) {
            $this->c->errorLogger->addError("Error Code: " . $ex->getCode() . " - File: " . $ex->getFile() . " - Line: " . $ex->getLine(), array($ex->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $ex->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        } catch (\Error $er) {
            $this->c->errorLogger->addError("Error Code: " . $er->getCode()  . " - File: " . $er->getFile() . " - Line: " . $er->getLine(), array($er->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $er->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        }
    }
    
    public function getLogoImg()
    {
        try {
            $logo_img = $this->getPageData();
        
            return $logo_img->logo_img;
        } catch (Exception $ex) {
            $this->c->errorLogger->addError("Error Code: " . $ex->getCode() . " - File: " . $ex->getFile() . " - Line: " . $ex->getLine(), array($ex->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $ex->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        } catch (\Error $er) {
            $this->c->errorLogger->addError("Error Code: " . $er->getCode()  . " - File: " . $er->getFile() . " - Line: " . $er->getLine(), array($er->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $er->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        }
    }
    
    public function getCategories($request, $response) 
    {
        try {
            $categories = BaseController::getColumn('categories', 'category_active', '=', 1);
            if($categories) {
                return $categories;
            }            
        } catch (\Illuminate\Database\QueryException $ex) {
            $this->c->errorLogger->addError("Error Code: " . $ex->getCode()  . " - File: " . $ex->getFile() . " - Line: " . $ex->getLine(), array($ex->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $ex->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        } catch (\BadMethodCallException $bex) {
            $this->c->errorLogger->addError("Error Code: " . $bex->getCode()  . " - File: " . $bex->getFile() . " - Line: " . $bex->getLine(), array($bex->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $bex->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        } catch (\Error $er) {
            $this->c->errorLogger->addError("Error Code: " . $er->getCode()  . " - File: " . $er->getFile() . " - Line: " . $er->getLine(), array($er->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $er->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        }
    }
    
    public function getProducts($request, $response) 
    {
        try {
            $products = AdminProduct::where('products.product_active', '=', 1)
                ->where('products.product_featured', '=', 1)
                ->orderBy('products.product_order', 'asc')
                ->get();
            if($products){
                return $products;
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            $this->c->errorLogger->addError("Error Code: " . $ex->getCode()  . " - File: " . $ex->getFile() . " - Line: " . $ex->getLine(), array($ex->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $ex->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        } catch (\BadMethodCallException $bex) {
            $this->c->errorLogger->addError("Error Code: " . $bex->getCode()  . " - File: " . $bex->getFile() . " - Line: " . $bex->getLine(), array($bex->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $bex->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        } catch (\Error $er) {
            $this->c->errorLogger->addError("Error Code: " . $er->getCode()  . " - File: " . $er->getFile() . " - Line: " . $er->getLine(), array($er->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $er->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        }
        
    }
    
    public function showCategory($request, $response, $args)
    {
        try {
            $locale = $args['locale'];
            $translation = $this->translation($request, $response, $args);        
            $cat_slug = $args['slug'];
            $category = BaseController::getColumn('categories', 'category_slug', '=', $cat_slug);

            $cat_length = count($category); // should be always 1

            for($i = 0; $i < $cat_length; $i++) {
                $categoriesTitleUnserialized[$i] = unserialize($category[$i]->category_title);
                $categoriesDescUnserialized[$i] = unserialize($category[$i]->category_desc);
            }

            $categoryId = $category[0]->id;
            $subcats = AdminSubcategory::
                    join('categories', 'categories.id', '=', 'subcategories.subcategory_category_id')
                    ->where('subcategories.subcategory_category_id', '=', $categoryId)
                    ->where('subcategories.subcategory_active', '=', 1)
                    ->orderBy('subcategories.subcategory_position', 'asc')
                    ->get();

            $subcat_length = count($subcats);

            for($i = 0; $i < $subcat_length; $i++) {
                $subcategoryTitlesUnserialized[$i] = unserialize($subcats[$i]->subcategory_title);
                $subcategoryDescsUnserialized[$i] = unserialize($subcats[$i]->subcategory_desc);
            }

            if(empty($this->getLogoImg())) {
                return $this->c->view->render($response, '/comtechbs/user_data.twig', [
                    'page_title' => $this->getPageTitle(),
                    'logo_name' => $this->getPageLogoName(),
                    'path' => '',
                    'subcats' => $subcats,
                    'subcat_title' => $subcategoryTitlesUnserialized,
                    'subcat_desc' => $subcategoryDescsUnserialized,
                    'categories' => $category,
                    'cat_title' => $categoriesTitleUnserialized,
                    'cat_desc' => $categoriesDescUnserialized,
                    'cat_slug' => $cat_slug,
                    'locale' => $locale,
                    'messages' => $translation,
                    'size' => $subcat_length,
                ]);
            }

            return $this->c->view->render($response, '/comtechbs/user_data.twig', [
                'page_title' => $this->getPageTitle(),
                'logo_name' => $this->getPageLogoName(),
                'logo_img' => $this->getLogoImg(),
                'path' => '/restaurant-slim-v1/public/img/logo/',
                //'path' => '/restaurant-v1_1/public/img/logo/', // linux
                'subcats' => $subcats,
                'subcat_title' => $subcategoryTitlesUnserialized,
                'subcat_desc' => $subcategoryDescsUnserialized,
                'categories' => $category,
                'cat_title' => $categoriesTitleUnserialized,
                'cat_desc' => $categoriesDescUnserialized,
                'cat_slug' => $cat_slug,
                'locale' => $locale,
                'messages' => $translation,
                'size' => $subcat_length,
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            $this->c->errorLogger->addError("Error Code: " . $ex->getCode()  . " - File: " . $ex->getFile() . " - Line: " . $ex->getLine(), array($ex->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $ex->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        } catch (\BadMethodCallException $bex) {
            $this->c->errorLogger->addError("Error Code: " . $bex->getCode()  . " - File: " . $bex->getFile() . " - Line: " . $bex->getLine(), array($bex->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $bex->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        } catch (\Error $er) {
            $this->c->errorLogger->addError("Error Code: " . $er->getCode()  . " - File: " . $er->getFile() . " - Line: " . $er->getLine(), array($er->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $er->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        }
    }
    
    public function showSubcategory($request, $response, $args)
    {
        try {
            $locale = $args['locale'];
            $translation = $this->translation($request, $response, $args);   
            $subcat_slug = $args['slug'];

            $subcategory = BaseController::getColumn('subcategories', 'subcategory_slug', '=', $subcat_slug);
            $subcategoryTitlesUnserialized = unserialize($subcategory[0]->subcategory_title);
            $subcategoryDescsUnserialized = unserialize($subcategory[0]->subcategory_desc);

            $subcategoryId = $subcategory[0]->id;

            $categoryId = $subcategory[0]->subcategory_category_id;
            $categories = BaseController::getColumn('categories','id', '=', $categoryId);

            $categoriesTitleUnserialized = unserialize($categories[0]->category_title);
            $categoriesDescUnserialized = unserialize($categories[0]->category_desc);

            $products = BaseController::getColumn('products', 'product_subcategory_id', '=', $subcategoryId);
            $prod_length = count($products);

            for($i = 0; $i < $prod_length; $i++) {
                $productTitleUnserialized[$i] = unserialize($products[$i]->product_title);
            }

            if(empty($this->getLogoImg())) {
                return $this->c->view->render($response, '/comtechbs/user_subcategory.twig', [
                    'page_title' => $this->getPageTitle(),
                    'logo_name' => $this->getPageLogoName(),
                    'path' => '',
                    'subcats' => $subcategory[0],
                    'subcat_title' => $subcategoryTitlesUnserialized,
                    'subcat_desc' => $subcategoryDescsUnserialized,
                    'categories' => $categories[0],
                    'cat_title' => $categoriesTitleUnserialized,
                    'cat_desc' => $categoriesDescUnserialized,
                    'products' => $products,
                    'prod_title' => $productTitleUnserialized,
                    'locale' => $locale,
                    'messages' => $translation,
                ]);
            }

            return $this->c->view->render($response, '/comtechbs/user_subcategory.twig', [
                'page_title' => $this->getPageTitle(),
                'logo_name' => $this->getPageLogoName(),
                'logo_img' => $this->getLogoImg(),
                'path' => '/restaurant-slim-v1/public/img/logo/',
                //'path' => '/restaurant-v1_1/public/img/logo/', // linux
                'subcats' => $subcategory,
                'subcat_title' => $subcategoryTitlesUnserialized,
                'subcat_desc' => $subcategoryDescsUnserialized,
                'categories' => $categories[0],
                'cat_title' => $categoriesTitleUnserialized,
                'cat_desc' => $categoriesDescUnserialized,
                'products' => $products,
                'prod_title' => $productTitleUnserialized,
                'locale' => $locale,
                'messages' => $translation,
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            $this->c->errorLogger->addError("Error Code: " . $ex->getCode()  . " - File: " . $ex->getFile() . " - Line: " . $ex->getLine(), array($ex->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $ex->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        } catch (\BadMethodCallException $bex) {
            $this->c->errorLogger->addError("Error Code: " . $bex->getCode()  . " - File: " . $bex->getFile() . " - Line: " . $bex->getLine(), array($bex->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $bex->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        } catch (\Error $er) {
            $this->c->errorLogger->addError("Error Code: " . $er->getCode()  . " - File: " . $er->getFile() . " - Line: " . $er->getLine(), array($er->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $er->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        }
    }
    
    public function showProduct($request, $response, $args)
    {
        try {
            $locale = $args['locale'];
            $translation = $this->translation($request, $response, $args);   
            $prod_slug = $args['slug'];
            $product = BaseController::getColumn('products', 'product_slug', '=', $prod_slug);
            $productTitleUnserialized = unserialize($product[0]->product_title);
            $productDescUnserialized = unserialize($product[0]->product_desc);

            $category_id = $product[0]->product_category_id;
            $subcategory_id = $product[0]->product_subcategory_id;

            $categories = BaseController::getColumn('categories', 'id', '=', $category_id);
            $categoryTitleUnserialized = unserialize($categories[0]->category_title);

            $subcategories = BaseController::getColumn('subcategories', 'id', '=', $subcategory_id);
            $subcategoryTitleUnserialized = unserialize($subcategories[0]->subcategory_title);

            $prod_path = $this->c->get('product_upload_directory');

            if(empty($this->getLogoImg())) {
                return $this->c->view->render($response, '/comtechbs/user_product.twig', [
                    'page_title' => $this->getPageTitle(),
                    'logo_name' => $this->getPageLogoName(),
                    'path' => '',
                    'subcat_title' => $subcategoryTitleUnserialized,
                    'cat_title' => $categoryTitleUnserialized,
                    'categories' => $categories[0],
                    'subcategories' => $subcategories[0],                
                    'products' => $product,
                    'prod_title' => $productTitleUnserialized,
                    'prod_desc' => $productDescUnserialized,
                    'product_path' => $prod_path,
                    'locale' => $locale,
                    'messages' => $translation,
                ]);
            }

            return $this->c->view->render($response, '/comtechbs/user_product.twig', [
                'page_title' => $this->getPageTitle(),
                'logo_name' => $this->getPageLogoName(),
                'logo_img' => $this->getLogoImg(),
                'path' => '/restaurant-slim-v1/public/img/logo/',
                //'path' => '/restaurant-v1_1/public/img/logo/', // linux
                'subcat_title' => $subcategoryTitleUnserialized,
                'cat_title' => $categoryTitleUnserialized,
                'categories' => $categories[0],
                'subcategories' => $subcategories[0],
                'products' => $product,
                'prod_title' => $productTitleUnserialized,
                'prod_desc' => $productDescUnserialized,
                'product_path' => $prod_path,
                'locale' => $locale,
                'messages' => $translation,
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            $this->c->errorLogger->addError("Error Code: " . $ex->getCode()  . " - File: " . $ex->getFile() . " - Line: " . $ex->getLine(), array($ex->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $ex->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        } catch (\BadMethodCallException $bex) {
            $this->c->errorLogger->addError("Error Code: " . $bex->getCode()  . " - File: " . $bex->getFile() . " - Line: " . $bex->getLine(), array($bex->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $bex->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        } catch (\Error $er) {
            $this->c->errorLogger->addError("Error Code: " . $er->getCode()  . " - File: " . $er->getFile() . " - Line: " . $er->getLine(), array($er->getMessage()));
            die("<b>Κωδικός Σφάλματος</b>: <u>" . $er->getCode() . "</u> Παρακαλούμε επικοινωνήστε με την υποστήριξη!");
        }        
    }    
}