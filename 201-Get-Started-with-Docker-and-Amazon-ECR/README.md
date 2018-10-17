
# Get Started with Docker & Amazon ECR

In this tutorial, we will create a Docker image which provides web service, push the image to Amazon Elastic Container Registry ([Amazon ECR](https://aws.amazon.com/ecr/)) which is a fully-managed Docker container registry.

## Prerequisite

Make sure the region is **US East (N. Virginia)**, which its short name is **us-east-1**.

## Set Up AWS Cloud9 Environment
In this lab, we use AWS Cloud9 which is a cloud IDE intergrating programming languages and useful tools. A cloud9 environment is based on an EC2 instance. We can  develope applications with a browser anywhere.

* Sign in to the AWS Management Console, and then open [AWS Cloud9 console](https://console.aws.amazon.com/cloud9/).

* If prompted, type the email address for the AWS account root user, and then choose Next.

* If a welcome page is displayed, for **New AWS Cloud9 environment**, choose **Create environment**. Otherwise, choose **Create environment**.

* On the **Name environment**	page, **type a name** for your environment. Optionally add a description to your environment.

* Leave everything as default and click **Next Step**.

* Click **Create environment**. It might take 30~60 seconds to create your environment.

* Because we want to accomplish access control by attaching a role ourself, we need to **turn off** the Cloud9 temporarily provided IAM credentials first.
![disableCredential.png](../images/disableCredential.png)

* In [Amazon EC2 console](https://console.aws.amazon.com/ec2/v2/home?#Instances:sort=instanceId), right-click the EC2 instance named with **`aws-cloud9`** prefix and click **Instance Settings** -> **Attach/Replace IAM Role**.
![attachRole.png](../images/attachRole.png)

* Click **Create new IAM role**.

* Click **Create role**.

* Click **EC2** then click **Next: Permissions**. Because Cloud9 is based on Amazon EC2, therefore we need to choose EC2.

* Search and select **AmazonEC2ContainerRegistryFullAccess** then Click **Next: Review**.

* In **Role Name** field, type **AllowEC2AccessECR** and click **Create Role**.

* Back to Attach/Replace IAM Role panel, click **Refresh** button, **select the role we just create** and click **Apply**.
![selectRole.png](../images/selectRole.png)


## Create a Docker Image
[Amazon ECS Task Definitions](https://docs.aws.amazon.com/AmazonECS/latest/developerguide/task_definitions.html) use Docker images to launch containers on the container instances in the cluster. In this section, we create a Docker image of a simple web application, and test it on local system or EC2 instance, and then push the image to a container registry (such as Amazon ECR or Docker Hub) so that we can use it in an ECS task definition.


* In [AWS Cloud9 console](https://console.aws.amazon.com/cloud9/), click **Open IDE** buttom for the environent which we created.

* In Cloud9 environment, we can use terminal in the lower panel. ![terminalBlock.png](../images/terminalBlock.png)

* Verify that whether Docker is installed in Cloud9 environment with following command. In general, Cloud9 has installed Docker by default and therefore we don't need to install ourself. 
	
	  docker version

  The output is supposed to be like as following:
  
  ![dockerVersion.png](../images/dockerVersion.png)


* Use **_vi_** text editor to create and edit a file called **_Dockerfile_**.  A *Dockerfile* is a manifest that describes the base image to use for the Docker image and what we want installed and running on it.

	  vi Dockerfile
    

* Press **`i`** key to enter insert mode and add the following script. The script will be executed while building Docker image and Apache web server will be set up. 

      FROM ubuntu:12.04

      # Install dependencies
      RUN apt-get update -y
      RUN apt-get install -y apache2

      # Install apache and write hello world message
      RUN echo "Hello World!" > /var/www/index.html

      # Configure apache
      RUN a2enmod rewrite
      RUN chown -R www-data:www-data /var/www
      ENV APACHE_RUN_USER www-data
      ENV APACHE_RUN_GROUP www-data
      ENV APACHE_LOG_DIR /var/log/apache2

      EXPOSE 80

      CMD ["/usr/sbin/apache2", "-D",  "FOREGROUND"]

* Press **`ESC`** key to return to command mode.

* Type **`:wq!`** to save and exit.

	  :wq!

* Build the Docker image from *Dockerfile*.
  >Note: "my_web_server" is the docker image name and "./" means the Dockerfile can be found in current directory.

	  docker build -t my_web_server ./
    
* List docker images to verify whether the image was created correctly. We should be able to see there is an image called **_my_web_server_**.

	  docker image ls

    ![dockerImages.png](../images/dockerImages.png)
 
* Run the newly built image. The **`–p 80:80`** option maps the exposed port 80 on the container to port 80 on the host system.

	  docker run -p 80:80 my_web_server

  We should be able to see the following message with an **IP address** which is the web server's IP address.
  >Note: we can ignore the ”Could not reliably determine the server's fully qualified domain name” message from Apache web server.
  
  ![run.png](../images/run.png)

* Open another terminal window by pressing **`alt + t`** key or clicking the **`+`** button and **`New Terminal`**.
  ![newTerminal.png](../images/newTerminal.png)

* Use **_Curl_** to fetch the index page from the **IP address** displayed in previous one step. We should be able to see an **_Hello World!_** message displayed on the screen.

      curl [Server IP]

  ![curl.png](../images/curl.png)

## Create a Repository for ECS

* In the **AWS Management Console**, on the **service** menu, click **Elastic Container Service**.

* Confirm that we are in **N.Virginia** region.

* Click **Repositories** on left panel.

* Click **Create Repository**.

* Type Repository name: **my_web_server**

* Click **Next step**.

* Click **Done** Once the repository has been created.

* The web page will automatically jump to the page of the repository. **Copy the Repository URI and paste to a text file** because we will use the URI later.

  ![URI.png](../images/URI.png)



## Tag Image and Push to Amazon ECR
* Back to Cloud9 environment.

* After creating an ECR repository, type **`docker` `tag` `my_web_server:latest` `[Repository URI]:latest`** to tag the image so that we can push it to our repository later.

	  docker tag my_web_server:latest 2421xxxxxxxx.dkr.ecr.us-east-1.amazonaws.com/my_web_server:latest

	>Note: the first “my_web_server” is the name of docker image, "latest" is the tag which represents the image is the latest version, “2421xxxxxxxx” is the AWS account ID, "us-east-1" is the region and the second “my_web_server” is the repository we created in previous step.

* Before pushing the image to our remote repository on Amazon ECR, we need to login to Amazon ECR first. Type `aws ecr get-login --no-include-email --region us-east-1` to get the login command from AWS.

      aws ecr get-login --no-include-email --region us-east-1

* **Copy** the output command.

  ![login.png](../images/login.png)

* **Paste** the command in terminal and **execute**, we should be able to see **_Login Succeeded_** message.

* After login, type **`docker` `push` `[Repository URI]:latest`** to push this image to our newly created repository on Amazon ECR:
		
      docker push 2421xxxxxxxx.dkr.ecr.us-east-1.amazonaws.com/my_web_server:latest
    
	>Note: “my_web_server” is the repository we created in previous step.

	![push.png](../images/push.png)






## Conclusion

Congratulations! We now have learned how to:

* Setup a Docker image.
* Create a image repository on Amazon ECR.
* Tag and push image to repository.

## Cleaning Resources

To avoid excessive billing, please delete the following resources when you have finished your practice.

* Cloud9 environment
* Image repository on ECR

## Next Level

* [202-Get-Started-with-Amazon-ECS-Service](../202-Get-Started-with-Amazon-ECS-Service/README.md)