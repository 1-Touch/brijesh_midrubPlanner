<?php
/**
 * Categories Helpers
 *
 * This file contains the class categories
 * with methods to process the categories data
 *
 * @author Scrisoft
 * @package Midrub
 * @since 0.0.7.6
 */

// Define the page namespace
namespace MidrubBase\User\Apps\Collection\Achieve\Helpers;

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Categories class provides the methods to process the categories data
 * 
 * @author Scrisoft
 * @package Midrub
 * @since 0.0.7.6
*/
class Categories {
    
    /**
     * Class variables
     *
     * @since 0.0.7.6
     */
    protected $CI;

    /**
     * Initialise the Class
     *
     * @since 0.0.7.6
     */
    public function __construct() {
        
        // Get codeigniter object instance
        $this->CI =& get_instance();
        
        // Load the lists model
        $this->CI->load->ext_model( MIDRUB_BASE_USER_APPS_ACHIEVE . 'models/', 'Achieve_lists_model', 'achieve_lists_model' );
        
    }
    
    /**
     * The public method achieve_create_new_category creates a new category
     * 
     * @since 0.0.7.5
     * 
     * @return void
     */ 
    public function achieve_create_new_category() {

        //echo "<pre>"; print_r("Test"); die;

         // Check if data was submitted
        if ($this->CI->input->post()) {
            
            // Add form validation
            //$this->CI->form_validation->set_rules('category_name', 'Category Name', 'trim|required');
            $this->CI->form_validation->set_rules('first_name', 'First Name', 'trim|required');
            $this->CI->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
            $this->CI->form_validation->set_rules('email', 'Email', 'trim|required');
            $this->CI->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required');

            
            // Get data
            $first_name = $this->CI->input->post('first_name');
            $last_name = $this->CI->input->post('last_name');
            $email = $this->CI->input->post('email');
            $phone_number = $this->CI->input->post('phone_number');
            
            
            if ( $this->CI->form_validation->run() == false ) {
                
                $data = array(
                    'success' => FALSE,
                    'message' => $this->CI->lang->line('please_enter_valid_category')
                );

                echo json_encode($data);   
                
            } else {
                
                // Create a new category
                $save_category= $this->CI->achieve_lists_model->save_category( $this->CI->user_id, $first_name, $last_name, $email, $phone_number);
                
                if ( $save_category ) {
                
                    $data = array(
                        'success' => TRUE,
                        'message' => $this->CI->lang->line('category_was_saved')
                    );

                    echo json_encode($data);
                
                } else {
                    
                    $data = array(
                        'success' => FALSE,
                        'message' => $this->CI->lang->line('category_was_not_saved')
                    );

                    echo json_encode($data); 
                    
                }
                
            }
            
        }
        
    }
    
    /**
     * The public method get_categories gets all media's categories
     * 
     * @since 0.0.7.5
     * 
     * @return void
     */ 
    public function get_categories() {

        // Gets all categories
        $all_categories= $this->CI->achieve_lists_model->get_categories( $this->CI->user_id, 'achieve' );

        if ( $all_categories ) {

            $data = array(
                'success' => TRUE,
                'categories' => $all_categories
            );

            echo json_encode($data);

        } else {

            $data = array(
                'success' => FALSE
            );

            echo json_encode($data); 

        }
        
    }  
    
    /**
     * The public method adds_medias_to_category adds medias to category
     * 
     * @since 0.0.7.5
     * 
     * @return void
     */ 
    public function adds_medias_to_category() {
        
        // Check if data was submitted
        if ($this->CI->input->post()) {
            
            // Add form validation
            $this->CI->form_validation->set_rules('category_id', 'Category ID', 'trim|integer|required');
            $this->CI->form_validation->set_rules('medias', 'Medias', 'trim');
            
            // Get data
            $category_id = $this->CI->input->post('category_id');
            $medias = $this->CI->input->post('medias');
            
            if ( $this->CI->form_validation->run() == false ) {
                
                $data = array(
                    'success' => FALSE,
                    'message' => $this->CI->lang->line('please_select_a_category')
                );

                echo json_encode($data);  
                exit();
                
            } else {
                
                // Load Media Model
                $this->CI->load->model('media');
                
                // Verify if user has the category
                if ( $this->CI->achieve_lists_model->if_user_has_category($this->CI->user_id, $category_id, 'achieve') ) {
                    
                    $count = 0;
                    
                    foreach ( $medias as $media ) {
                        
                        // Verify if user has the media
                        if ( $this->CI->media->single_media($this->CI->user_id, $media) ) {
                        
                            // Add media to category
                            if ( $this->CI->achieve_lists_model->save_media_category($category_id, $this->CI->user_id, $media) ) {
                                $count++;
                            }
                            
                        }
                        
                    }

                    if ( $count ) {

                        $data = array(
                            'success' => TRUE,
                            'message' => $count . ' ' . $this->CI->lang->line('media_added_to_category')
                        );

                        echo json_encode($data);

                    } else {

                        $data = array(
                            'success' => FALSE,
                            'message' => $count . ' ' . $this->CI->lang->line('media_added_to_category')
                        );

                        echo json_encode($data); 

                    }
                    
                    exit();
                    
                }
                
            }
            
        }
        
        $data = array(
            'success' => FALSE,
            'message' => $this->CI->lang->line('error_occurred')
        );

        echo json_encode($data);         
        
    }
    
    /**
     * The public method remove_from_category removes medias from category
     * 
     * @since 0.0.7.6
     * 
     * @return void
     */ 
    public function remove_from_category() {
        
        // Check if data was submitted
        if ($this->CI->input->post()) {
            
            // Add form validation
            $this->CI->form_validation->set_rules('category_id', 'Category ID', 'trim|integer|required');
            $this->CI->form_validation->set_rules('medias', 'Medias', 'trim');
            
            // Get data
            $category_id = $this->CI->input->post('category_id');
            $medias = $this->CI->input->post('medias');
            
            if ( $this->CI->form_validation->run() == false ) {
                
                $data = array(
                    'success' => FALSE,
                    'message' => $this->CI->lang->line('please_select_a_category')
                );

                echo json_encode($data);  
                exit();
                
            } else {
                
                // Load Media Model
                $this->CI->load->model('media');
                
                // Verify if user has the category
                if ( $this->CI->achieve_lists_model->if_user_has_category($this->CI->user_id, $category_id, 'achieve') ) {
                    
                    $count = 0;
                    
                    foreach ( $medias as $media ) {
                        
                        // Verify if user has the media
                        if ( $this->CI->media->single_media($this->CI->user_id, $media) ) {
                        
                            // Add media to category
                            if ( $this->CI->achieve_lists_model->delete_from_category($this->CI->user_id, $category_id, $media) ) {
                                $count++;
                            }
                            
                        }
                        
                    }

                    if ( $count ) {

                        $data = array(
                            'success' => TRUE,
                            'message' => $count . ' ' . $this->CI->lang->line('media_removed_from_category')
                        );

                        echo json_encode($data);

                    } else {

                        $data = array(
                            'success' => FALSE,
                            'message' => $count . ' ' . $this->CI->lang->line('media_removed_from_category')
                        );

                        echo json_encode($data); 

                    }
                    
                    exit();
                    
                }
                
            }
            
        }
        
        $data = array(
            'success' => FALSE,
            'message' => $this->CI->lang->line('error_occurred')
        );

        echo json_encode($data);         
        
    }
    
    /**
     * The public method delete_media_category deletes media's category
     * 
     * @since 0.0.7.5
     * 
     * @return void
     */ 
    public function delete_media_category() {
        
        // Get category_id's input
        $category_id = $this->CI->input->get('category_id', TRUE);
        
        // Check if data was submitted
        if ($category_id) {
                
            // Load Media Model
            $this->CI->load->model('media');

            // Verify if user has the category
            if ( $this->CI->achieve_lists_model->if_user_has_category($this->CI->user_id, $category_id, 'achieve') ) {

                if ( $this->CI->achieve_lists_model->delete_category($this->CI->user_id, $category_id, 'achieve') ) {

                    $data = array(
                        'success' => TRUE,
                        'message' => $this->CI->lang->line('media_was_deleted')
                    );

                    echo json_encode($data);

                } else {

                    $data = array(
                        'success' => FALSE,
                        'message' => $this->CI->lang->line('media_was_not_deleted')
                    );

                    echo json_encode($data); 

                }
                
            }

            exit();
            
        }
        
        $data = array(
            'success' => FALSE,
            'message' => $this->CI->lang->line('error_occurred')
        );

        echo json_encode($data);         
        
    }    
    
}

