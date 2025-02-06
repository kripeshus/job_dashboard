<?php
  //error_reporting(E_ALL);
  //ini_set('display_errors', 1);
  require_once 'config.php';
  if(isset($_GET['dismiss'])){
    if (!isset($_SESSION['closed_job'])){
      $_SESSION['closed_job'] = [];
    }
    $_SESSION['closed_job'][] = $_GET['dismiss'];
  }
  $limit = 10;
  $page = $_GET['page'] ?? 1;
  $skip = ($page - 1) * $limit;
  
  $search = $_GET['search'] ?? '';
  $where = [];
  $params = [];
  $types = '';
  
  if($search){
    $where[] = '(title like ? or location like ?)';
    $params[] = '%'.$search.'%';
    $params[] = '%'.$search.'%';
    $types = 'ss';
  }
  
  $jobCountQuery = "SELECT COUNT(*) as total_count from jobs";
  if($where){
    $jobCountQuery .= ' where '.implode('and', $where);
  }
  $jobCount = $conn->prepare($jobCountQuery);
  if(!empty($params)){
    $jobCount->bind_param($types, ...$params);
  }
  $jobCount->execute();
  $totalJob = $jobCount->get_result();
  $obj = mysqli_fetch_object($totalJob);
  $total = $obj->total_count;
  $jobCount->close();
  $totalPage = ceil($total/$limit);
  $joblist = "SELECT * FROM jobs ";
  if($where){
    $joblist .= ' where '.implode('and', $where);
  }
  $joblist .= "order by created_date desc limit ? offset ?";
  $stmt = $conn->prepare($joblist);
  if(!empty($params)){
    $params[] = $limit;
    $params[] = $skip;
    $types .= 'ii';
    $stmt->bind_param($types, ...$params);
  } else {
    $stmt->bind_param("ii", $limit, $skip);
  }
  $stmt->execute();
  $result = $stmt->get_result();
  
  $closed = $_SESSION['closed_job'] ?? [];
  $featureJob = 'select * from jobs';
  if($closed){
    $featureJob .= ' where id not in ('.implode(',', $closed).')';
  }
  $featureJob .= ' order by RAND() limit 1';
  $feature = $conn->prepare($featureJob);
  $feature->execute();
  $feature = $feature->get_result();
  $feature = $feature->fetch_assoc();
  
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Jobs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </head>
  <body class="container">
    <h1>Jobs</h1><h3>-<a href="addjob.php">Post a job</a></h3>
    <div class="mt-5 mb-5">
     <form method="GET">
       <input type="text" name="search" placeholder="Search jobs">
       <button type="submit">Search</button>
     </form>
    </div>
    <div class="mt-5 mb-3 border 15">
      <div class="p-3">
        <h2>Featured</h2>
        <?php
          if (isset($feature['title']) && !empty($feature['title'])){
            echo '<h3>'.$feature['title'].'</h3>';
          }
        ?>
        <p>
        <?php
          echo $feature['description'];
        ?>
        </p>
        <span>Location: <?php echo $feature['location']; ?></span><br>
        <?php
          echo '<a href="?dismiss='.$feature['id'].'"> Dismiss </a>';
        ?>
        </div>
    </div>
    <?php
      while ($row = $result->fetch_assoc()){
        if(isset($row['title']) && !empty($row['title'])) {
    ?>
      <h3><?php echo $row['title'] ?></h3>
      <p>
      <?php
        echo $row['description'];
      ?>
      </p>
      <span>Location: <?php echo $row['location']; ?></span>
      <hr>
    <?php
        }
      }
    ?>
    <div class="d-flex flex-row mb-3">
    <?php
      for($i = 1; $i <= $totalPage; $i++){
        echo '<div class="p-2"><a href="?page='.$i.'">'.$i.'</a></div>';
      }
    ?>
    </div>
  </body>
</html>
