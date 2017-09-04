<?php
class form{

    private $action;

    public function __construct(){

        $this->action="http://localhost/wordpress/?post_type=product";

    }

    public function forum(){

        echo("<html>
                <body>
                    <form border=0 action='".$this->action."' method='post'>
                        <table border=1>
                            <tr>
                             <td><input id='inputBox' type='text' name='metadata' placeholder='Quick search'/></td>
                             <td><input type='submit' value='Go' id='myid' /></td>
                            </tr>
                        </table>
                    </form>
                </body>
                <style>
                    
                    #inputBox{
                        border :none;
                        color : black;
                        text-size : 20px;
                        border-radius : 5px;
                        box-shadow : 7px 3px 8px 0.6;
                        width : 300px;
                        text-style : bold;  
                    }
                    #inputBox:hover{
                    width : 320px;
                    }
                    #myid{
                        color : #ff5656;
                        text-size : 14px;
                        border-radius : 5px;
                        box-shadow : 5px 4px 5px 0.6;
                        width : 100px;
                        
                        }
                    #myid:hover{background-color : rgba(23 , 44, 53 , 0,6); 
                    width : 105px;
                    }
                </style>

            </html>");

    }

}
?>

