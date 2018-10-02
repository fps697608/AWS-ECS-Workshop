
# Get Started with Docker & Amazon ECR

In this lab, We will create a Docker image which provides a simple web application, push the image to Amazon Elastic Container Registry ([Amazon ECR](https://aws.amazon.com/tw/ecr/)) which is a fully-managed Docker container registry and run a container on Amazon Elastic Container Service([Amazon ECS](https://aws.amazon.com/tw/ecs)).  

## Prepare Cloud9 & CICD environment

>Make sure you are in US East (N. Virginia), which short name is us-east-1.

### Setup AWS Cloud9 environment
In this lab, we use AWS Cloud9 which is a cloud IDE intergrating programming languages and useful tools. A cloud9 environment is based on an EC2 instance. We can  develope applications with a browser anywhere.

* Sign in to the AWS Management Console, and then open [AWS Cloud9 console](https://console.aws.amazon.com/cloud9/).
* If prompted, type the email address for the AWS account root user, and then choose Next.
* If a welcome page is displayed, for **New AWS Cloud9 environment**, choose **Create environment**. Otherwise, choose **Create environment**.
* On the **Name environment**	page, **type a name** for your environment. Optionally add a description to your environment.
* Leave everything as default and click **Next Step**.
* Click **Create environment**. (It would  take 30~60 seconds to create your environment.)
* Because we want to accomplish access control by attaching a role ourself, we need to **turn off** the Cloud9 temporarily provided IAM credentials first.
![disableCredential.png](images/disableCredential.png)
* In [Amazon EC2 console](https://console.aws.amazon.com/ec2/v2/home?#Instances:sort=instanceId), right-click the EC2 instance named with "**aws-cloud9**" prefix and click **Instance Settings** -> **Attach/Replace IAM Role**.
![attachRole.png](images/role.png)
* Click **Create new IAM role**.
* Click **Create role**.
* Click **EC2** then click **Next: Permissions**. Because Cloud9 is based on Amazon EC2, therefore we need to choose EC2.
* Search and select "**AmazonEC2ContainerRegistryFullAccess**" then Click **Next: Review**.
* For **Role Name** field, type "**AllowEC2AccessECR**" and click **Create Role**.
* Back to Attach/Replace IAM Role panel, click **Refresh** button, **select the role we just create** and click  **Apply**.
![selectRole.png](images/selectRole.png)


### Create a Docker Image
[Amazon ECS Task Definitions](https://docs.aws.amazon.com/AmazonECS/latest/developerguide/task_definitions.html) use Docker images to launch containers on the container instances in the cluster. In this section, we create a Docker image of a simple web application, and test it on local system or EC2 instance, and then push the image to a container registry (such as Amazon ECR or Docker Hub) so that we can use it in an ECS task definition.


* In [AWS Cloud9 console](https://console.aws.amazon.com/cloud9/), click **Open IDE** buttom for the environent which we created.
* In Cloud9 environment, we can use terminal in the lower panel. ![terminalBlock.png](images/terminalBlock.png)
* Verify that whether Docker is installed in Cloud9 environment with following command. In general, Cloud9 has installed Docker by default and therefore we don't need to install ourself. 
	
	  docker version

  The output should be like as following:
  
  ![dockerVersion.png](images/dockerVersion.png)


* Use *vi* text editor to create and edit a file called "**_Dockerfile_**".  A *Dockerfile* is a manifest that describes the base image to use for the Docker image and what we want installed and running on it.

	  vi Dockerfile
    

* Press "**i**" key to enter insert mode and add the following content:

      FROM ubuntu:12.04

      # Install dependencies
      RUN apt-get update -y
      RUN apt-get install -y apache2

      # Install apache and write hellow world message
      RUN echo "Hello World!" > /var/www/index.html

      # Configure apache
      RUN a2enmod rewrite
      RUN chown -R www-data:www-data /var/www
      ENV APACHE_RUN_USER www-data
      ENV APACHE_RUN_GROUP www-data
      ENV APACHE_LOG_DIR /var/log/apache2

      EXPOSE 80

      CMD ["/usr/sbin/apache2", "-D",  "FOREGROUND"]


* Press "**ESC**" key to return to command mode.
* Type "**:wq!**" to save and exit.

	  :wq!

* Build the Docker image from *Dockerfile*.
  >Note: the “hello-world” is the docker image name.

	  docker build -t hello-world .
    
* List docker images to verify whether the image was created correctly. We should be able to see there is a image called "**hello-world**".

	  docker image ls

    ![dockerImages.png](images/dockerImages.png)
 
* Run the newly built image. The **_–p 80:80_** option maps the exposed port 80 on the container to port 80 on the host system.

	  docker run -p 80:80 hello-world

  We should be able to see the following message with a IP address which is the web server's IP address.
  >Note: we can ignore the ”Could not reliably determine the server's fully qualified domain name” message from Apache web server.
  
  ![run.png](images/run.png)

* Use **_Curl_** to fetch the index page from the **IP address** displayed in previous one step. We should be able to see a **_Hello World!_** message displayed on the screen.

      curl [Server IP]

  ![curl.png](images/curl.png)

### Create a Repository for ECS

* In the **AWS Management Console**, on the **service** menu, click **Elastic Container Service**.

* Confirm that we are in **N.Virginia** region.

* If a welcome page is displayed, click **Get started**, otherwise, click **Create repository**.

* Type Repository name: **docker-demo**

* Click **Next step**.

* We will see **Successfully created repository** message on the page.


### Tag Image and Push it to Amazon ECR

* After creating an ECR repository, tag the image so we can push the image to our repository.

	  docker tag hello-world:latest 2421964xxxxx.dkr.ecr.us-east-1.amazonaws.com/docker-demo:latest

	>Note: “hello-world” is the name of docker image, “2421964xxxxx” is the AWS account ID, "us-east-1" is the region and “docker-demo” is the repository we created in previous step.


	Run the following command to push this image to our newly created AWS repository:
		
      docker push 2421964xxxxx.dkr.ecr.us-east-1.amazonaws.com/docker-demo:latest
    
	>Note: “docker-demo” is the repository we created in previous step.

	![push.png](images/push.png)

### Create a Cluster in Amazon ECS
Before running container on Amazon ECS, we need to create a cluster first. In Amazon ECS, we can create a Fargate cluster which is managed by AWS or create an EC2 cluster which we need to manage container instance ourself.

* In the **AWS Management Console**, on the **service** menu, click **Elastic Container Service**.

* On the left panel, click **Clusters**.

* Click **Create Cluster**.

* From here you can create a **Fargate cluster** or create an **EC2 cluster**.

#### Fargate Cluster
* Click **Networking only** and Click **Next step**.

* Type a **Cluster Name**.

* In **Networking** part, it's optional to create a new VPC for the cluster. In this tutorial, we create a new VPC here hence we **click the checkbox of create VPC**.
* In **CIDR Block**, type **20.0.0.0/16**.
* In **Subnet 1**, type **20.0.0.0/24**.
* In **Subnet 2**, type **20.0.1.0/24**.
* Click **create** and wait for the creation.

### Create a Task Definition for Amazon ECS

* Back to AWS ECS console.

* Click **Task Definitions** on left panel.

* Click **Create new Task Definition**.
###


* Leave all the task definition setting as default, click **Next Step**.

* Leave all the configure service and network access as default, click **Next Step**.

* Confirm and leave the configure cluster and security group as default, click **Review & launch**.

* Click **Launch instance & run service**.

* Wait EC2 instance status – 0 of 13 complete.


### Exam the Resource

* In the **AWS Management Console**, on the **service** menu, click **EC2**.

* Click Instance, select **ECS Instance-EC2ContainerService-default** instance.

* On the **Description** tab in the lower pane, copy the **IPv4 Public IP** to the clipboard.

* Open a new browser tab, paste the IP address from the clipboard and hit Enter. The Container should appear.

* In the browser, we should be able to see:

	**_Hello World!_**




## Conclusion

Congratulations! We now have learned how to:

* Setup a Docker engine.
* Create a container repository.
* Tag and push image to Amazon ECR.
* Run image on Amazon ECS.
